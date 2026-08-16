<?php

namespace App\Http\Resources\Event;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class EventScheduleDayResource extends JsonResource
{
    /**
     * $this->resource array:
     * [
     *   'date' => 'Y-m-d',
     *   'schedules' => Collection<EventSchedule>
     * ]
     */
    public function toArray($request)
    {
        $lang = $request->header('Accept-Language', $request->query('lang', 'en'));
        $lang = in_array(strtolower($lang), ['ar','en'], true) ? strtolower($lang) : 'en';

        $date  = $this['date'];
        $label = $date ? Carbon::parse($date)->locale($lang)->isoFormat('D MMMM YYYY') : null;

        // نمرر الجلسات إلى Resource مع تحميل المتحدثين لو مش محمّلة
        $schedules = $this['schedules'];

        return [
            'date'      => $date,   // Y-m-d
            'label'     => $label,  // "2 September 2025" أو بالعربي حسب الهيدر
            'schedules' => EventScheduleResource::collection($schedules),
        ];
    }
}
