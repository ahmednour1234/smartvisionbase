<?php

namespace App\Imports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ClientsImport implements ToModel, WithHeadingRow, WithChunkReading
{
    protected int $formId;
    protected int $added = 0;
    protected int $skipped = 0;

    public function __construct(int $formId)
    {
        $this->formId = $formId;
    }

    public function model(array $row)
    {
        // تحقق من وجود الاسم
        if (empty($row['name'])) {
            $this->skipped++;
            return null;
        }

        // تخطي إذا الإيميل موجود مسبقًا
        if (!empty($row['email']) && Client::where('email', $row['email'])->exists()) {
            $this->skipped++;
            return null;
        }

        $this->added++;

        return new Client([
            'name'         => $row['name'],
            'email'        => $row['email'] ?? null,
            'phone'        => $row['phone'] ?? null,
            'job'          => $row['job'] ?? null,
            'form_id'      => $this->formId,
            'active'       => 1,
            'type'         => 1,
            'country_code' => $row['country_code'] ?? null,
            'code'         => uniqid('CL-'),
        ]);
    }

    public function chunkSize(): int
    {
        return 500; // يعالج 500 صف في كل مرة
    }

    public function getAddedCount(): int
    {
        return $this->added;
    }

    public function getSkippedCount(): int
    {
        return $this->skipped;
    }
}
