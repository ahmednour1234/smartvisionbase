<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Sponsor;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SponsorScheduleService
{
    /** مواعيد ثابتة */
    public const DAYS          = ['2025-11-05', '2025-11-06'];
    public const START_TIME    = '10:30'; // بداية اليوم
    public const END_TIME      = '17:30'; // نهاية اليوم
    public const SLOT_MINUTES  = 30;      // طول السلوّت بالدقائق
    public const SLOT_CAPACITY = 4;       // السعة لكل سلوّت

    /**
     * توليد سلوّتات ثابتة (id = index تصاعدي)
     */
    public function staticSlots(): Collection
    {
        $all = collect();

        foreach (self::DAYS as $day) {
            $start = Carbon::parse("$day " . self::START_TIME);
            $end   = Carbon::parse("$day " . self::END_TIME);

            // فترات بنصف ساعة
            $period = CarbonPeriod::create($start, self::SLOT_MINUTES . ' minutes', $end);

            foreach ($period as $slotStart) {
                $slotEnd = (clone $slotStart)->addMinutes(self::SLOT_MINUTES);
                if ($slotEnd->gt($end)) {
                    break;
                }

                $all->push([
                    // هنضيف id لاحقًا بعد ما نخلص التجميع (عشان يبقى Index متسق)
                    'date'      => $slotStart->toDateString(),         // Y-m-d
                    'start'     => $slotStart->toTimeString(),         // HH:MM:SS
                    'end'       => $slotEnd->toTimeString(),           // HH:MM:SS
                    'start_at'  => $slotStart->toDateTimeString(),     // Y-m-d H:i:s
                    'end_at'    => $slotEnd->toDateTimeString(),
                    'capacity'  => self::SLOT_CAPACITY,
                ]);
            }
        }

        // إسناد id = index ابتداءً من 1 بنفس ترتيب العناصر
        return $all->values()->map(function ($slot, $i) {
            $slot['id'] = $i + 1; // index يبدأ من 1
            return $slot;
        });
    }

    /**
     * جدول مواعيد سبونسر معيّن + الإتاحة
     * $filters = [
     *   'date'      => 'Y-m-d',
     *   'status'    => 'pending|confirmed|cancelled',
     *   'time_from' => 'HH:MM',
     *   'time_to'   => 'HH:MM'
     * ]
     */
    public function sponsorSlots(int $sponsorId, array $filters = []): Collection
    {
        $slots = $this->staticSlots();

        // فلترة على مستوى السلوّتات المنتَجة
        if (!empty($filters['date'])) {
            $slots = $slots->where('date', $filters['date'])->values();
        }
        if (!empty($filters['time_from'])) {
            $slots = $slots->filter(fn ($s) => $s['start'] >= ($filters['time_from'] . ':00'))->values();
        }
        if (!empty($filters['time_to'])) {
            $slots = $slots->filter(fn ($s) => $s['end'] <= ($filters['time_to'] . ':00'))->values();
        }

        // إحصاء الحجوزات لكل سلوّت
        return $slots->map(function ($slot) use ($sponsorId, $filters) {
            $q = Booking::query()
                ->where('sponsor_id', $sponsorId)
                ->whereDate('time_from', $slot['date'])
                ->whereTime('time_from', '>=', $slot['start'])
                ->whereTime('time_from', '<',  $slot['end']);

            // فلتر الحالة على مستوى الاستعلام العام (اختياري)
            if (!empty($filters['status'])) {
                $q->where('status', $filters['status']);
            }

            // إحصائيات دقيقة لكل حالة
            $pendingCount   = (clone $q)->where('status', 'pending')->count();
            $confirmedCount = (clone $q)->where('status', 'confirmed')->count();
            $cancelledCount = (clone $q)->where('status', 'cancelled')->count();

            // السعة تُستهلك من pending + confirmed
            $consumed  = $pendingCount + $confirmedCount;
            $remaining = max(0, self::SLOT_CAPACITY - $consumed);
            $available = $remaining > 0 ? 1 : 0;

            return [
                'id'              => $slot['id'],                    // index
                'sponsor_id'      => $sponsorId,
                'date'            => $slot['date'],
                'start'           => substr($slot['start'], 0, 5),   // HH:MM
                'end'             => substr($slot['end'], 0, 5),
                'capacity'        => self::SLOT_CAPACITY,
                'pending'         => $pendingCount,
                'confirmed'       => $confirmedCount,
                'cancelled'       => $cancelledCount,
                'remaining'       => $remaining,
                'available'       => $available, // 1 = متاح / 0 = غير متاح
            ];
        });
    }

    /**
     * فلترة حجوزات عميل معيّن (للاسترجاع في Resource)
     * filters: date, time_from, time_to, status
     */
    public function clientBookings(int $clientId, array $filters = []): LengthAwarePaginator
    {
        $q = Booking::query()->where('client_id', $clientId);

        if (!empty($filters['status'])) {
            $q->where('status', $filters['status']);
        }
        if (!empty($filters['date'])) {
            $q->whereDate('time_from', $filters['date']);
        }
        if (!empty($filters['time_from'])) {
            $q->whereTime('time_from', '>=', $filters['time_from'] . ':00');
        }
        if (!empty($filters['time_to'])) {
            $q->whereTime('time_from', '<=', $filters['time_to'] . ':00');
        }

        return $q->latest('time_from')->paginate(20);
    }

    /**
     * جميع السلوّتات لكل/لمجموعة سبونسرز
     * $filters تقبل نفس مفاتيح sponsorSlots + sponsor_id اختياري
     */
    public function allSponsorsSlots(array $filters = []): array
    {
        $query = Sponsor::query()->select('id', 'name_en');

        if (!empty($filters['sponsor_id'])) {
            $query->where('id', (int) $filters['sponsor_id']);
        }

        $sponsors = $query->get();

        $out = [];
        foreach ($sponsors as $sp) {
            $out[] = [
                'sponsor' => [
                    'id'   => $sp->id,
                    'name' => $sp->name_en,
                ],
                // كل عنصر داخل slots فيه: id (index) + sponsor_id + بقية الحقول
                'slots'   => $this->sponsorSlots($sp->id, $filters)->values(),
            ];
        }

        return $out;
    }
}
