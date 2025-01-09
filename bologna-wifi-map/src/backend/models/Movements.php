<?php

trait Movements
{
    public function addMovement(
        string $date,
        string $day,
        int    $fiftiethPercentile00, int $fiftiethPercentile01, int $fiftiethPercentile02, int $fiftiethPercentile03,
        int    $fiftiethPercentile04, int $fiftiethPercentile05, int $fiftiethPercentile06, int $fiftiethPercentile07,
        int    $fiftiethPercentile08, int $fiftiethPercentile09, int $fiftiethPercentile10, int $fiftiethPercentile11,
        int    $fiftiethPercentile12, int $fiftiethPercentile13, int $fiftiethPercentile14, int $fiftiethPercentile15,
        int    $fiftiethPercentile16, int $fiftiethPercentile17, int $fiftiethPercentile18, int $fiftiethPercentile19,
        int    $fiftiethPercentile20, int $fiftiethPercentile21, int $fiftiethPercentile22, int $fiftiethPercentile23,
        int    $totPass00, int $totPass01, int $totPass02, int $totPass03,
        int    $totPass04, int $totPass05, int $totPass06, int $totPass07,
        int    $totPass08, int $totPass09, int $totPass10, int $totPass11,
        int    $totPass12, int $totPass13, int $totPass14, int $totPass15,
        int    $totPass16, int $totPass17, int $totPass18, int $totPass19,
        int    $totPass20, int $totPass21, int $totPass22, int $totPass23,
        string $zoneIdTo,
        string $zoneIdFrom
    ): bool
    {
        $query = "insert into movements(
                        date, 
                        day, 
                        percentile_50_00, percentile_50_01, percentile_50_02, percentile_50_03, 
                        percentile_50_04, percentile_50_05, percentile_50_06, percentile_50_07, 
                        percentile_50_08, percentile_50_09, percentile_50_10, percentile_50_11, 
                        percentile_50_12, percentile_50_13, percentile_50_14, percentile_50_15, 
                        percentile_50_16, percentile_50_17, percentile_50_18, percentile_50_19, 
                        percentile_50_20, percentile_50_21, percentile_50_22, percentile_50_23, 
                        tot_pass_00, tot_pass_01, tot_pass_02, tot_pass_03, 
                        tot_pass_04, tot_pass_05, tot_pass_06, tot_pass_07, 
                        tot_pass_08, tot_pass_09, tot_pass_10, tot_pass_11, 
                        tot_pass_12, tot_pass_13, tot_pass_14, tot_pass_15, 
                        tot_pass_16, tot_pass_17, tot_pass_18, tot_pass_19, 
                        tot_pass_20, tot_pass_21, tot_pass_22, tot_pass_23, 
                        zone_id_to, 
                        zone_id_from
                  ) 
                  values (
                        ?,
                        ?,
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?,
                        ?
                  )";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param(
            "ssiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiss",
            $date,
            $day,
            $fiftiethPercentile00, $fiftiethPercentile01, $fiftiethPercentile02, $fiftiethPercentile03,
            $fiftiethPercentile04, $fiftiethPercentile05, $fiftiethPercentile06, $fiftiethPercentile07,
            $fiftiethPercentile08, $fiftiethPercentile09, $fiftiethPercentile10, $fiftiethPercentile11,
            $fiftiethPercentile12, $fiftiethPercentile13, $fiftiethPercentile14, $fiftiethPercentile15,
            $fiftiethPercentile16, $fiftiethPercentile17, $fiftiethPercentile18, $fiftiethPercentile19,
            $fiftiethPercentile20, $fiftiethPercentile21, $fiftiethPercentile22, $fiftiethPercentile23,
            $totPass00, $totPass01, $totPass02, $totPass03,
            $totPass04, $totPass05, $totPass06, $totPass07,
            $totPass08, $totPass09, $totPass10, $totPass11,
            $totPass12, $totPass13, $totPass14, $totPass15,
            $totPass16, $totPass17, $totPass18, $totPass19,
            $totPass20, $totPass21, $totPass22, $totPass23,
            $zoneIdTo,
            $zoneIdFrom
        );
        return $stmt->execute();
    }

    public function getLastMovementsDate(): string|null
    {
        $query = "select max(date)
                  from movements";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $stmt->bind_result($lastDate);
        $stmt->fetch();
        return $lastDate;
    }
}