<?php

namespace App\Services\Recruitment;

use App\Models\Jabatan;
use App\Models\PeriodeRekrutmen;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class PositionSynchronizer
{
    /**
     * Synchronize positions without replacing existing records and their IDs.
     *
     * @param  array<int, string>  $positionNames
     * @param  array<int, string|null>  $jobTitles
     * @param  array<int, int|string|null>  $jobIds
     * @return array<int, int>
     */
    public function synchronize(
        PeriodeRekrutmen $recruitmentPeriod,
        array $positionNames,
        array $jobTitles,
        array $jobIds,
    ): array {
        $existingJobs = Jabatan::query()
            ->where('periode_rekrutmen_id', $recruitmentPeriod->id)
            ->get()
            ->keyBy('id');

        $submittedIds = collect($jobIds)
            ->filter()
            ->map(fn ($id) => (int) $id);

        if ($submittedIds->diff($existingJobs->keys())->isNotEmpty()) {
            throw ValidationException::withMessages([
                'jabatan_ids' => 'Satu atau lebih jabatan tidak termasuk dalam periode rekrutmen ini.',
            ]);
        }

        $this->deleteRemovedJobs($existingJobs, $submittedIds);

        $jobIdsByInputIndex = [];

        foreach ($jobTitles as $index => $jobTitle) {
            if (blank($jobTitle)) {
                continue;
            }

            $attributes = [
                'nama_posisi' => filled($positionNames[$index] ?? null) ? trim($positionNames[$index]) : '-',
                'nama_jabatan' => trim($jobTitle),
            ];

            $jobId = $jobIds[$index] ?? null;
            if ($jobId) {
                $job = $existingJobs->get((int) $jobId);
                $job->update($attributes);
            } else {
                $job = Jabatan::create([
                    'periode_rekrutmen_id' => $recruitmentPeriod->id,
                    ...$attributes,
                ]);
            }

            $jobIdsByInputIndex[$index] = $job->id;
        }

        return $jobIdsByInputIndex;
    }

    /**
     * @param  Collection<int, Jabatan>  $existingJobs
     * @param  Collection<int, int>  $submittedIds
     */
    private function deleteRemovedJobs(Collection $existingJobs, Collection $submittedIds): void
    {
        $removedJobs = $existingJobs->except($submittedIds->all());

        foreach ($removedJobs as $job) {
            $hasReferences = $job->pendaftaranPilihanPertama()->exists()
                || $job->pendaftaranPilihanKedua()->exists()
                || $job->tugas()->exists();

            if ($hasReferences) {
                throw ValidationException::withMessages([
                    'jabatan_ids' => "Jabatan '{$job->nama_jabatan}' tidak dapat dihapus karena sudah digunakan oleh data pendaftaran atau tugas.",
                ]);
            }
        }

        $removedJobs->each->delete();
    }
}
