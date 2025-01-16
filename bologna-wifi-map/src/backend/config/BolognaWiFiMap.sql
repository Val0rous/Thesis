create database bologna_wifi_map;
use bologna_wifi_map;


create table areas
(
    zone_id          varchar(64)     not null primary key, -- 128 max
    zone_name        varchar(64)     not null,             -- 128 max
    -- geo_shape_type          varchar(16)     not null,             -- all have "type": "Feature"
    -- geo_shape_geometry_type varchar(16)     not null,             -- all have "type": "Polygon"
    geo_point_2d_lat decimal(17, 15) not null,
    geo_point_2d_lon decimal(17, 15) not null
);

create table coordinates
(
    coordinate_id int unsigned auto_increment not null primary key,
    latitude      decimal(17, 15)             not null,
    longitude     decimal(17, 15)             not null,
    `order`       tinyint unsigned            not null,
    zone_id       varchar(64)                 not null,
    foreign key (zone_id) references areas (zone_id)
);

create table crowding_attendance
(
    -- daily values for all 24 hours
    crowding_attendance_id int unsigned auto_increment not null primary key,
    date                   char(10)                    not null, -- DST not accounted for. Save everything using standard time. Later adjust via software to account for it, but keep it like this at db level
    day                    varchar(16)                 not null,
    avg_crowding_00        smallint unsigned           not null,
    avg_crowding_01        smallint unsigned           not null,
    avg_crowding_02        smallint unsigned           not null,
    avg_crowding_03        smallint unsigned           not null,
    avg_crowding_04        smallint unsigned           not null,
    avg_crowding_05        smallint unsigned           not null,
    avg_crowding_06        smallint unsigned           not null,
    avg_crowding_07        smallint unsigned           not null,
    avg_crowding_08        smallint unsigned           not null,
    avg_crowding_09        smallint unsigned           not null,
    avg_crowding_10        smallint unsigned           not null,
    avg_crowding_11        smallint unsigned           not null,
    avg_crowding_12        smallint unsigned           not null,
    avg_crowding_13        smallint unsigned           not null,
    avg_crowding_14        smallint unsigned           not null,
    avg_crowding_15        smallint unsigned           not null,
    avg_crowding_16        smallint unsigned           not null,
    avg_crowding_17        smallint unsigned           not null,
    avg_crowding_18        smallint unsigned           not null,
    avg_crowding_19        smallint unsigned           not null,
    avg_crowding_20        smallint unsigned           not null,
    avg_crowding_21        smallint unsigned           not null,
    avg_crowding_22        smallint unsigned           not null,
    avg_crowding_23        smallint unsigned           not null,
    avg_attendance_00      smallint unsigned           not null,
    avg_attendance_01      smallint unsigned           not null,
    avg_attendance_02      smallint unsigned           not null,
    avg_attendance_03      smallint unsigned           not null,
    avg_attendance_04      smallint unsigned           not null,
    avg_attendance_05      smallint unsigned           not null,
    avg_attendance_06      smallint unsigned           not null,
    avg_attendance_07      smallint unsigned           not null,
    avg_attendance_08      smallint unsigned           not null,
    avg_attendance_09      smallint unsigned           not null,
    avg_attendance_10      smallint unsigned           not null,
    avg_attendance_11      smallint unsigned           not null,
    avg_attendance_12      smallint unsigned           not null,
    avg_attendance_13      smallint unsigned           not null,
    avg_attendance_14      smallint unsigned           not null,
    avg_attendance_15      smallint unsigned           not null,
    avg_attendance_16      smallint unsigned           not null,
    avg_attendance_17      smallint unsigned           not null,
    avg_attendance_18      smallint unsigned           not null,
    avg_attendance_19      smallint unsigned           not null,
    avg_attendance_20      smallint unsigned           not null,
    avg_attendance_21      smallint unsigned           not null,
    avg_attendance_22      smallint unsigned           not null,
    avg_attendance_23      smallint unsigned           not null,
    zone_id                varchar(64)                 not null,
    foreign key (zone_id) references areas (zone_id)
);

create table movements
(
    -- daily values for all 24 hours
    movement_id      int unsigned auto_increment not null primary key,
    date             char(10)                    not null,
    day              varchar(16)                 not null,
    percentile_50_00 smallint unsigned           not null,
    percentile_50_01 smallint unsigned           not null,
    percentile_50_02 smallint unsigned           not null,
    percentile_50_03 smallint unsigned           not null,
    percentile_50_04 smallint unsigned           not null,
    percentile_50_05 smallint unsigned           not null,
    percentile_50_06 smallint unsigned           not null,
    percentile_50_07 smallint unsigned           not null,
    percentile_50_08 smallint unsigned           not null,
    percentile_50_09 smallint unsigned           not null,
    percentile_50_10 smallint unsigned           not null,
    percentile_50_11 smallint unsigned           not null,
    percentile_50_12 smallint unsigned           not null,
    percentile_50_13 smallint unsigned           not null,
    percentile_50_14 smallint unsigned           not null,
    percentile_50_15 smallint unsigned           not null,
    percentile_50_16 smallint unsigned           not null,
    percentile_50_17 smallint unsigned           not null,
    percentile_50_18 smallint unsigned           not null,
    percentile_50_19 smallint unsigned           not null,
    percentile_50_20 smallint unsigned           not null,
    percentile_50_21 smallint unsigned           not null,
    percentile_50_22 smallint unsigned           not null,
    percentile_50_23 smallint unsigned           not null,
    tot_pass_00      smallint unsigned           not null,
    tot_pass_01      smallint unsigned           not null,
    tot_pass_02      smallint unsigned           not null,
    tot_pass_03      smallint unsigned           not null,
    tot_pass_04      smallint unsigned           not null,
    tot_pass_05      smallint unsigned           not null,
    tot_pass_06      smallint unsigned           not null,
    tot_pass_07      smallint unsigned           not null,
    tot_pass_08      smallint unsigned           not null,
    tot_pass_09      smallint unsigned           not null,
    tot_pass_10      smallint unsigned           not null,
    tot_pass_11      smallint unsigned           not null,
    tot_pass_12      smallint unsigned           not null,
    tot_pass_13      smallint unsigned           not null,
    tot_pass_14      smallint unsigned           not null,
    tot_pass_15      smallint unsigned           not null,
    tot_pass_16      smallint unsigned           not null,
    tot_pass_17      smallint unsigned           not null,
    tot_pass_18      smallint unsigned           not null,
    tot_pass_19      smallint unsigned           not null,
    tot_pass_20      smallint unsigned           not null,
    tot_pass_21      smallint unsigned           not null,
    tot_pass_22      smallint unsigned           not null,
    tot_pass_23      smallint unsigned           not null,
    zone_id_to       varchar(64)                 not null,
    zone_id_from     varchar(64)                 not null,
    foreign key (zone_id_to) references areas (zone_id),
    foreign key (zone_id_from) references areas (zone_id)
);
