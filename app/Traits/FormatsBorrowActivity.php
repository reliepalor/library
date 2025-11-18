<?php

namespace App\Traits;

use Illuminate\Support\Collection;

trait FormatsBorrowActivity
{
    /**
     * Resolve the attendee activity string with the latest borrow request status.
     *
     * @param  mixed  $attendance
     * @param  \Illuminate\Support\Collection  $borrowRequests
     * @param  string|int  $identifier
     * @return string|null
     */
    protected function getActivityWithBorrowStatus($attendance, Collection $borrowRequests, $identifier)
    {
        $activity = $attendance->activity;
        $userBorrowRequests = $borrowRequests->get($identifier, collect());

        $linkedBorrowRequests = $userBorrowRequests->where('attendance_id', $attendance->id);

        if ($linkedBorrowRequests->isNotEmpty()) {
            $mostRecentRequest = $linkedBorrowRequests->sortByDesc('created_at')->first();

            switch ($mostRecentRequest->status) {
                case 'pending':
                    $activity = 'Wait for approval';
                    break;
                case 'approved':
                    $originalActivity = $mostRecentRequest->original_activity ?? 'Borrow';
                    $activityPrefix = ($originalActivity === 'Stay&Borrow') ? 'Stay&Borrow:' : 'Borrow:';
                    $activity = $mostRecentRequest->book
                        ? $activityPrefix . $mostRecentRequest->book->book_code
                        : $attendance->activity;
                    break;
                case 'rejected':
                    $activity = 'Borrow book rejected';
                    break;
                case 'returned':
                    $activity = 'Book returned';
                    break;
            }
        }

        return $activity;
    }
}

