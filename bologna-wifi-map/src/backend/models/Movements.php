<?php

trait Movements
{
    /**
     * @param string $date
     * @param string $day
     * @param int[] $fiftiethPercentile
     * @param int[] $totPass
     * @param string $zoneIdTo
     * @param string $zoneIdFrom
     * @return bool
     */
    public function addMovement(
        string $date,
        string $day,
        array  $fiftiethPercentile,
        array  $totPass,
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
            $fiftiethPercentile[0], $fiftiethPercentile[1], $fiftiethPercentile[2], $fiftiethPercentile[3],
            $fiftiethPercentile[4], $fiftiethPercentile[5], $fiftiethPercentile[6], $fiftiethPercentile[7],
            $fiftiethPercentile[8], $fiftiethPercentile[9], $fiftiethPercentile[10], $fiftiethPercentile[11],
            $fiftiethPercentile[12], $fiftiethPercentile[13], $fiftiethPercentile[14], $fiftiethPercentile[15],
            $fiftiethPercentile[16], $fiftiethPercentile[17], $fiftiethPercentile[18], $fiftiethPercentile[19],
            $fiftiethPercentile[20], $fiftiethPercentile[21], $fiftiethPercentile[22], $fiftiethPercentile[23],
            $totPass[0], $totPass[1], $totPass[2], $totPass[3],
            $totPass[4], $totPass[5], $totPass[6], $totPass[7],
            $totPass[8], $totPass[9], $totPass[10], $totPass[11],
            $totPass[12], $totPass[13], $totPass[14], $totPass[15],
            $totPass[16], $totPass[17], $totPass[18], $totPass[19],
            $totPass[20], $totPass[21], $totPass[22], $totPass[23],
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