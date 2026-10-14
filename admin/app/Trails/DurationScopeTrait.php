<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait DurationScopeTrait
{

    public function scopeDuration($query, $value, $isLast = false, $forExpenses = false)
    {
        $date = now(); // Get the current date and time

        if ($isLast) {
            // Adjust the date if looking for the "last" period
            if ('year' == $value) {
                $date = $date->subYear();
            } elseif ('month' == $value) {
                $date = $date->subMonth();
            } elseif ('day' == $value) {
                $date = $date->subDay();
            }
        }
        // No need to check if for expenses 
        if (!$forExpenses) {
            $query = $query->whereNotNull('completed_at');
        }
        // For 'day', compare the full date (including year, month, and day)
        if ('day' == $value) {
            return $query->whereDate('created_at', '=', $date->toDateString()); // Exact match for the full date (today)
        }

        // For 'month', compare the month and ensure the year matches
        if ('month' == $value) {
            return $query->whereYear('created_at', '=', $date->year)
                ->whereMonth('created_at', '=', $date->month);
        }

        // For 'year', compare only the year
        if ('year' == $value) {
            return $query->whereYear('created_at', '=', $date->year);
        }

        return $query;
    }


    public function scopeAdvanceDuration(Builder $query, $interval, $startDate = null, $startTime = null, $endDate = null, $endTime = null)
    {
        $timezone = config('app.timezone');
        // Combine date and time strings into datetime strings and apply the timezone
        $startDateTime = $startDate
            ? Carbon::parse($startDate . ' ' . ($startTime ?? '00:00:00'), $timezone)
            : Carbon::now($timezone)->startOfDay();

        $endDateTime = $endDate
            ? Carbon::parse($endDate . ' ' . ($endTime ?? '23:59:59'), $timezone)
            : Carbon::now($timezone)->endOfDay();

        // Adjust Carbon instances to use the provided timezone
        $startCarbon = Carbon::parse($startDateTime)->setTimezone($timezone);
        $endCarbon = Carbon::parse($endDateTime)->setTimezone($timezone);

        switch ($interval) {
            case 'today':
                return $query->whereBetween('created_at', [$startCarbon, $endCarbon]);
            case 'yesterday':
                return $query->whereDate('created_at', Carbon::yesterday());
            case 'weekly':
                return $query->whereBetween('created_at', [$startCarbon->startOfWeek(), $endCarbon->endOfWeek()]);
            case 'monthly':
                return $query->whereYear('created_at', $startCarbon->year)
                    ->whereMonth('created_at', $startCarbon->month);
            case 'last_month':
                return $query->whereYear('created_at', Carbon::now()->subMonth()->year)
                    ->whereMonth('created_at', Carbon::now()->subMonth()->month);
            case 'yearly':
                return $query->whereYear('created_at', $startCarbon->year);
            case 'last_three_months':
                $threeMonthsAgo = Carbon::now()->subMonths(3);
                return $query->whereBetween('created_at', [$threeMonthsAgo->startOfMonth(), $endCarbon]);
            case 'last_year':
                return $query->whereYear('created_at', Carbon::now()->subYear()->year);
            case 'last_seven_days':
                return $query->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()]);
            case 'last_six_months':
                return $query->whereBetween('created_at', [Carbon::now()->subMonths(6), Carbon::now()]);
            case 'last_quarter':
                return $query->whereBetween('created_at', [Carbon::now()->startOfQuarter()->subQuarter(), Carbon::now()->endOfQuarter()->subQuarter()]);
            case 'last_biannual':
                return $query->whereBetween('created_at', [Carbon::now()->startOfYear()->subMonths(6), Carbon::now()]);
            case 'last_biweek':
                return $query->whereBetween('created_at', [Carbon::now()->subWeeks(2), Carbon::now()]);
            case 'last_work_week':
                return $query->whereBetween('created_at', [Carbon::now()->previous(Carbon::MONDAY), Carbon::now()->previous(Carbon::FRIDAY)]);
            case 'custom':
                return $query->whereBetween('created_at', [
                    $startDateTime,
                    $endDateTime
                ]);
            default:
                return $query; // or throw an exception for invalid interval
        }
    }
}
