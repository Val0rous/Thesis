<?php

function convertToReadableTime(float $seconds): string
{
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $seconds = $seconds - ($hours * 3600) - ($minutes * 60);

    return sprintf("%02dh %02dm %06.3fs", $hours, $minutes, $seconds);
}