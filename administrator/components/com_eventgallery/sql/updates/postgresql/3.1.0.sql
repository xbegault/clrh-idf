CREATE TABLE IF NOT EXISTS `#__eventgallery_watermark` (
  "id" serial NOT NULL,
  "name" varchar(45) DEFAULT NULL,
  "description" text DEFAULT NULL,
  "image" text(45) DEFAULT NULL,
  "image_position" varchar(4) DEFAULT NULL,
  "image_margin_horizontal" int(4) DEFAULT NULL,
  "image_margin_vertical" int(4) DEFAULT NULL,
  "image_mode" varchar(20) DEFAULT NULL,
  "image_mode_prop" int(4) DEFAULT NULL,
  "image_opacity" int(4) DEFAULT NULL,
  "published" smallint NOT NULL DEFAULT '0',
  "ordering" integer NOT NULL DEFAULT 0,
  "modified" timestamp without time zone NULL DEFAULT NULL,
  "created" timestamp without time zone NULL DEFAULT NULL,
  
  PRIMARY KEY ("id")
);

ALTER TABLE  "#__eventgallery_folder" ADD  "watermarkid" int(11) AFTER "imagetypesetid";