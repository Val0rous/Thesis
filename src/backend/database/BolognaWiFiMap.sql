-- Database Section
-- ________________ 

create database bologna_wifi_map;
use bologna_wifi_map;


-- Tables Section
-- _____________ 

create table crowding_attendance (
    -- daily values for all 24 hours
    crowding_attendance_id not null, -- Index attribute not implemented -- not null,
    date char(25) not null,
    day varchar(1) not null,
    avg_crowding_00 int not null,
    avg_crowding_01 int not null,
    avg_crowding_02 int not null,
    avg_crowding_03 int not null,
    avg_crowding_04 int not null,
    avg_crowding_05 int not null,
    avg_crowding_06 int not null,
    avg_crowding_07 int not null,
    avg_crowding_08 int not null,
    avg_crowding_09 int not null,
    avg_crowding_10 int not null,
    avg_crowding_11 int not null,
    avg_crowding_12 int not null,
    avg_crowding_13 int not null,
    avg_crowding_14 int not null,
    avg_crowding_15 int not null,
    avg_crowding_16 int not null,
    avg_crowding_17 int not null,
    avg_crowding_18 int not null,
    avg_crowding_19 int not null,
    avg_crowding_20 int not null,
    avg_crowding_21 int not null,
    avg_crowding_22 int not null,
    avg_crowding_23 int not null,
    avg_attendance_00 int not null,
    avg_attendance_01 int not null,
    avg_attendance_02 int not null,
    avg_attendance_03 int not null,
    avg_attendance_04 int not null,
    avg_attendance_05 int not null,
    avg_attendance_06 int not null,
    avg_attendance_07 int not null,
    avg_attendance_08 int not null,
    avg_attendance_09 int not null,
    avg_attendance_10 int not null,
    avg_attendance_11 int not null,
    avg_attendance_12 int not null,
    avg_attendance_13 int not null,
    avg_attendance_14 int not null,
    avg_attendance_15 int not null,
    avg_attendance_16 int not null,
    avg_attendance_17 int not null,
    avg_attendance_18 int not null,
    avg_attendance_19 int not null,
    avg_attendance_20 int not null,
    avg_attendance_21 int not null,
    avg_attendance_22 int not null,
    avg_attendance_23 int not null,
    zone_id char(1) not null,
    constraint pk_crowding_attendance primary key (crowding_attendance_id));

create table area (
    zone_id varchar not null,
    zone_name varchar not null,
    geo_shape_type char(1) not null,
    geo_shape_geometry_type char(1) not null,
    geo_point_2d_lat char(1) not null,
    geo_point_2d_lon char(1) not null,
    constraint pk_area primary key (zone_id));

create table coordinates (
    coordinate_id not null, -- Index attribute not implemented -- not null,
    latitude float(1) not null,
    longitude float(1) not null,
    order char(1) not null,
    zone_id char(1) not null,
    constraint pk_coordinates primary key (coordinate_id));

create table movements (
    -- daily values for all 24 hours
    movement_id not null, -- Index attribute not implemented -- not null,
    date char(10) not null,
    day varchar(1) not null,
    percentile_50_00 int not null,
    percentile_50_01 int not null,
    percentile_50_02 int not null,
    percentile_50_03 int not null,
    percentile_50_04 int not null,
    percentile_50_05 int not null,
    percentile_50_06 int not null,
    percentile_50_07 int not null,
    percentile_50_08 int not null,
    percentile_50_09 int not null,
    percentile_50_10 int not null,
    percentile_50_11 int not null,
    percentile_50_12 int not null,
    percentile_50_13 int not null,
    percentile_50_14 int not null,
    percentile_50_15 int not null,
    percentile_50_16 int not null,
    percentile_50_17 int not null,
    percentile_50_18 int not null,
    percentile_50_19 int not null,
    percentile_50_20 int not null,
    percentile_50_21 int not null,
    percentile_50_22 int not null,
    percentile_50_23 int not null,
    tot_steps_00 int not null,
    tot_steps_01 int not null,
    tot_steps_02 int not null,
    tot_steps_03 int not null,
    tot_steps_04 int not null,
    tot_steps_05 int not null,
    tot_steps_06 int not null,
    tot_steps_07 int not null,
    tot_steps_08 int not null,
    tot_steps_09 int not null,
    tot_steps_10 int not null,
    tot_steps_11 int not null,
    tot_steps_12 int not null,
    tot_steps_13 int not null,
    tot_steps_14 int not null,
    tot_steps_15 int not null,
    tot_steps_16 int not null,
    tot_steps_17 int not null,
    tot_steps_18 int not null,
    tot_steps_19 int not null,
    tot_steps_20 int not null,
    tot_steps_21 int not null,
    tot_steps_22 int not null,
    tot_steps_23 int not null,
    zone_id_to char(1) not null,
    zone_id_from char(1) not null,
    constraint pk_movements primary key (movement_id));


-- Constraints Section
-- ___________________ 

alter table crowding_attendance add constraint fk_area
     foreign key (zone_id)
     references area (zone_id);

alter table coordinates add constraint fk_geometry
     foreign key (zone_id)
     references area (zone_id);

alter table movements add constraint fk_to
     foreign key (zone_id_to)
     references area (zone_id);

alter table movements add constraint fk_from
     foreign key (zone_id_from)
     references area (zone_id);


-- Index Section
-- _____________ 

