create database bologna_wifi_map;
use bologna_wifi_map;


create table areas
(
    zone_id                 varchar(64)     not null primary key, -- 128 max
    zone_name               varchar(64)     not null,             -- 128 max
    geo_shape_type          varchar(16)     not null,             -- all have "type": "Feature"
    geo_shape_geometry_type varchar(16)     not null,             -- all have "type": "Polygon"
    geo_point_2d_lat        decimal(17, 15) not null,
    geo_point_2d_lon        decimal(17, 15) not null
);

create table coordinates
(
    coordinate_id int unsigned auto_increment not null primary key,
    latitude      decimal(17, 15)             not null,
    longitude     decimal(17, 15)             not null,
    `order`       int unsigned                not null,
    zone_id       varchar(64)                 not null,
    foreign key (zone_id) references areas (zone_id)
);

create table crowding_attendance
(
    -- daily values for all 24 hours
    crowding_attendance_id int unsigned auto_increment not null primary key,
    date                   char(10)                    not null, -- DST not accounted for. Save everything using standard time. Later adjust via software to account for it, but keep it like this at db level
    day                    varchar(16)                 not null,
    avg_crowding_00        int unsigned                not null,
    avg_crowding_01        int unsigned                not null,
    avg_crowding_02        int unsigned                not null,
    avg_crowding_03        int unsigned                not null,
    avg_crowding_04        int unsigned                not null,
    avg_crowding_05        int unsigned                not null,
    avg_crowding_06        int unsigned                not null,
    avg_crowding_07        int unsigned                not null,
    avg_crowding_08        int unsigned                not null,
    avg_crowding_09        int unsigned                not null,
    avg_crowding_10        int unsigned                not null,
    avg_crowding_11        int unsigned                not null,
    avg_crowding_12        int unsigned                not null,
    avg_crowding_13        int unsigned                not null,
    avg_crowding_14        int unsigned                not null,
    avg_crowding_15        int unsigned                not null,
    avg_crowding_16        int unsigned                not null,
    avg_crowding_17        int unsigned                not null,
    avg_crowding_18        int unsigned                not null,
    avg_crowding_19        int unsigned                not null,
    avg_crowding_20        int unsigned                not null,
    avg_crowding_21        int unsigned                not null,
    avg_crowding_22        int unsigned                not null,
    avg_crowding_23        int unsigned                not null,
    avg_attendance_00      int unsigned                not null,
    avg_attendance_01      int unsigned                not null,
    avg_attendance_02      int unsigned                not null,
    avg_attendance_03      int unsigned                not null,
    avg_attendance_04      int unsigned                not null,
    avg_attendance_05      int unsigned                not null,
    avg_attendance_06      int unsigned                not null,
    avg_attendance_07      int unsigned                not null,
    avg_attendance_08      int unsigned                not null,
    avg_attendance_09      int unsigned                not null,
    avg_attendance_10      int unsigned                not null,
    avg_attendance_11      int unsigned                not null,
    avg_attendance_12      int unsigned                not null,
    avg_attendance_13      int unsigned                not null,
    avg_attendance_14      int unsigned                not null,
    avg_attendance_15      int unsigned                not null,
    avg_attendance_16      int unsigned                not null,
    avg_attendance_17      int unsigned                not null,
    avg_attendance_18      int unsigned                not null,
    avg_attendance_19      int unsigned                not null,
    avg_attendance_20      int unsigned                not null,
    avg_attendance_21      int unsigned                not null,
    avg_attendance_22      int unsigned                not null,
    avg_attendance_23      int unsigned                not null,
    zone_id                varchar(64)                 not null,
    foreign key (zone_id) references areas (zone_id)
);

create table movements
(
    -- daily values for all 24 hours
    movement_id      int unsigned auto_increment not null primary key,
    date             char(10)                    not null,
    day              varchar(16)                 not null,
    percentile_50_00 int unsigned                not null,
    percentile_50_01 int unsigned                not null,
    percentile_50_02 int unsigned                not null,
    percentile_50_03 int unsigned                not null,
    percentile_50_04 int unsigned                not null,
    percentile_50_05 int unsigned                not null,
    percentile_50_06 int unsigned                not null,
    percentile_50_07 int unsigned                not null,
    percentile_50_08 int unsigned                not null,
    percentile_50_09 int unsigned                not null,
    percentile_50_10 int unsigned                not null,
    percentile_50_11 int unsigned                not null,
    percentile_50_12 int unsigned                not null,
    percentile_50_13 int unsigned                not null,
    percentile_50_14 int unsigned                not null,
    percentile_50_15 int unsigned                not null,
    percentile_50_16 int unsigned                not null,
    percentile_50_17 int unsigned                not null,
    percentile_50_18 int unsigned                not null,
    percentile_50_19 int unsigned                not null,
    percentile_50_20 int unsigned                not null,
    percentile_50_21 int unsigned                not null,
    percentile_50_22 int unsigned                not null,
    percentile_50_23 int unsigned                not null,
    tot_pass_00      int unsigned                not null,
    tot_pass_01      int unsigned                not null,
    tot_pass_02      int unsigned                not null,
    tot_pass_03      int unsigned                not null,
    tot_pass_04      int unsigned                not null,
    tot_pass_05      int unsigned                not null,
    tot_pass_06      int unsigned                not null,
    tot_pass_07      int unsigned                not null,
    tot_pass_08      int unsigned                not null,
    tot_pass_09      int unsigned                not null,
    tot_pass_10      int unsigned                not null,
    tot_pass_11      int unsigned                not null,
    tot_pass_12      int unsigned                not null,
    tot_pass_13      int unsigned                not null,
    tot_pass_14      int unsigned                not null,
    tot_pass_15      int unsigned                not null,
    tot_pass_16      int unsigned                not null,
    tot_pass_17      int unsigned                not null,
    tot_pass_18      int unsigned                not null,
    tot_pass_19      int unsigned                not null,
    tot_pass_20      int unsigned                not null,
    tot_pass_21      int unsigned                not null,
    tot_pass_22      int unsigned                not null,
    tot_pass_23      int unsigned                not null,
    zone_id_to       varchar(64)                 not null,
    zone_id_from     varchar(64)                 not null,
    foreign key (zone_id_to) references areas (zone_id),
    foreign key (zone_id_from) references areas (zone_id)
);
