<?php

function convertToReadableTime(float $seconds): string
{
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $seconds = $seconds - ($hours * 3600) - ($minutes * 60);

    return sprintf("%02dh %02dm %06.3fs", $hours, $minutes, $seconds);
}

function extractHourFromTimestamp(string $timestamp): int
{
    // Extract the hour part between 'T' and the first ':'
    return (int)substr($timestamp, strpos($timestamp, 'T') + 1, 2);
}

function normalizeArrayTo24(array $input): array
{
    $targetLength = 24;
    $currentLength = count($input);

    if ($currentLength < $targetLength) {
        // Fill empty positions with zeros
        $input = array_merge($input, array_fill(0, $targetLength - $currentLength, 0));
    } elseif ($currentLength > $targetLength) {
        // Remove extra elements
        $input = array_slice($input, 0, $targetLength);
    }

    return $input;
}