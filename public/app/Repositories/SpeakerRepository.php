<?php
namespace App\Repositories;

use App\Models\Speaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class SpeakerRepository
{
    public function all()
    {
        return Speaker::all();
    }

    public function paginate($limit = 10)
    {
        return Speaker::latest()->paginate($limit);
    }

  public function create(array $data): Speaker
    {
        return DB::transaction(function () use ($data) {
            $eventId = $data['event_id'] ?? null;

            // لو جالك orders (إعادة ترتيب جماعي)
            if (!empty($data['orders']) && is_array($data['orders'])) {
                $this->reorder($data['orders'], $eventId);
                unset($data['orders']);
            }

            // لو حددت sort_order للسجل الجديد
            if (array_key_exists('orders', $data) && is_numeric($data['orders'])) {
                $newPos = (int) $data['orders'];

                $q = Speaker::query();
                if ($eventId) $q->where('event_id', $eventId);

                // نزحزح اللي بعده لتحت
                $q->where('orders', '>=', $newPos)->increment('orders');

                $speaker = Speaker::create($data);
                $this->normalizeOrder($eventId);
                return $speaker->fresh();
            }

            // بدون ترتيب محدد: ضيفه في آخر القائمة
            $q = Speaker::query();
            if ($eventId) $q->where('event_id', $eventId);
            $max = (int) $q->max('orders');

            $data['orders'] = $max + 1;

            $speaker = Speaker::create($data);
            // تأكيد الاتساق
            $this->normalizeOrder($eventId);

            return $speaker->fresh();
        });
    }

    /**
     * تحديث سبيكر.
     * - لو فيها orders => نعيد ترتيب الكل فقط (ونحدّث باقي الحقول).
     * - لو فيها sort_order => ننقله للمكان الجديد ونزحزح الباقي.
     * - لو لا => تحدّث الحقول العادية بس.
     */
    public function update(Speaker $speaker, array $data): Speaker
    {
        return DB::transaction(function () use ($speaker, $data) {
            $eventId = $data['event_id'] ?? $speaker->event_id;

            // 1) لو فيه orders: عيد ترتيب الكل (الموجودين فقط)، وبعدها حدّث باقي الحقول العادية
            if (!empty($data['orders']) && is_array($data['orders'])) {
                $this->reorder($data['orders'], $eventId);
                unset($data['orders']);
            }

            // 2) لو فيه sort_order منفردة: انقل السبيكر لموضع جديد
            if (array_key_exists('orders', $data) && is_numeric($data['orders'])) {
                $newPos = (int) $data['orders'];
                $oldPos = (int) $speaker->sort_order;

                if ($newPos !== $oldPos) {
                    $q = Speaker::query()->lockForUpdate();
                    if ($eventId) $q->where('event_id', $eventId);

                    if ($newPos < $oldPos) {
                        // ارفع مَنْ هم بين [newPos .. oldPos-1]
                        $q->whereBetween('orders', [$newPos, $oldPos - 1])->increment('orders');
                    } else {
                        // نزّل مَنْ هم بين [oldPos+1 .. newPos]
                        $q->whereBetween('orders', [$oldPos + 1, $newPos])->decrement('orders');
                    }

                    $speaker->orders = $newPos;
                }
            }

            // 3) حدّث باقي الحقول
            $speaker->fill(Arr::except($data, ['orders']))->save();

            // 4) رتّب الترتيب عشان يبقى 1..N بدون فراغات
            $this->normalizeOrder($eventId);

            return $speaker->fresh();
        });
    }

    /**
     * إعادة ترتيب جماعية.
     * تدعم شكلين:
     * - مصفوفة IDs بالترتيب المطلوب: [5, 3, 9, ...]
     * - مصفوفة خرائط: [['id'=>5,'order'=>1], ['id'=>3,'order'=>2], ...] أو [ 5=>1, 3=>2, ... ]
     */
    public function reorder(array $orders, ?int $eventId = null): void
    {
        DB::transaction(function () use ($orders, $eventId) {
            // حوّل أي صيغة إلى map: id => order
            $map = $this->normalizeOrdersToMap($orders);

            // قفل الصفوف أثناء الترتيب
            $q = Speaker::query()->select(['id', 'orders']);
            if ($eventId) $q->where('event_id', $eventId);
            $speakers = $q->lockForUpdate()->get();

            // عيّن القيم الجديدة لمن وُردت في الماب
            foreach ($speakers as $sp) {
                if (isset($map[$sp->id])) {
                    $sp->orders = (int) $map[$sp->id];
                }
            }

            // طبّع الترتيب ليبقى فريدًا ومتسلسلًا 1..N
            $speakers = $speakers->sortBy([
                ['orders', 'asc'],
                ['id', 'asc'],
            ])->values();

            $i = 1;
            foreach ($speakers as $sp) {
                if ($sp->orders !== $i) {
                    Speaker::whereKey($sp->id)->update(['orders' => $i]);
                }
                $i++;
            }
        });
    }

    /**
     * تطبيع الترتيب داخل سكوب الفاعلية (اختياري): يجعله 1..N بلا فراغات.
     */
    public function normalizeOrder(?int $eventId = null): void
    {
        $q = Speaker::query()->orderBy('orders')->orderBy('id');
        if ($eventId) $q->where('event_id', $eventId);

        $list = $q->lockForUpdate()->get(['id', 'orders']);
        $i = 1;
        foreach ($list as $sp) {
            if ((int)$sp->sort_order !== $i) {
                Speaker::whereKey($sp->id)->update(['orders' => $i]);
            }
            $i++;
        }
    }

    /**
     * Helper: يقبل صيغ مختلفة لـ orders ويُرجع map: [id => order]
     */
    private function normalizeOrdersToMap(array $orders): array
    {
        // شكل: [5,3,9] => 5=>1, 3=>2, 9=>3
        if (array_is_list($orders)) {
            // لو القيم كلها أعداد صحيحة (IDs)
            if (count(array_filter($orders, fn($v) => is_numeric($v))) === count($orders)) {
                $map = [];
                $i = 1;
                foreach ($orders as $id) {
                    $map[(int)$id] = $i++;
                }
                return $map;
            }
            // شكل: [['id'=>5,'order'=>1], ...]
            $assoc = [];
            foreach ($orders as $row) {
                if (is_array($row) && isset($row['id'])) {
                    $assoc[(int)$row['id']] = isset($row['order']) ? (int)$row['order'] : null;
                }
            }
            // لو في order ناقصة، هنرتبهم حسب إدخالهم
            $i = 1;
            foreach ($assoc as $id => $ord) {
                $assoc[$id] = $ord ?: $i++;
            }
            return $assoc;
        }

        // شكل: [ 5=>1, 3=>2, ... ]
        $map = [];
        foreach ($orders as $id => $ord) {
            $map[(int)$id] = (int)$ord;
        }
        return $map;
    }

    public function delete(Speaker $speaker)
    {
        return $speaker->delete();
    }
}
