CREATE TABLE "#__eventgallery_comment" (
  "id" serial NOT NULL,
  "file" varchar(125) NOT NULL,
  "folder" varchar(125) NOT NULL,
  "text" text NOT NULL,
  "name" varchar(255) NOT NULL,
  "user_id" varchar(255) NOT NULL,
  "ip" varchar(15) NOT NULL,
  "published" smallint NOT NULL default '1',
  "date" timestamp without time zone NOT NULL,
  "email" varchar(255) NOT NULL,
  "link" varchar(255) NOT NULL,
  "modified" timestamp without time zone NULL DEFAULT NULL,
  "created" timestamp without time zone NULL DEFAULT NULL, 
  PRIMARY KEY  ("id")
);
CREATE INDEX "#__eventgallery_comment_idx_filefolder" ON "#__eventgallery_comment" ("folder","file");

CREATE TABLE "#__eventgallery_file" (
  "id" serial NOT NULL,
  "folder" varchar(125) NOT NULL,
  "file" varchar(125) NOT NULL,
  "width" integer,
  "height" integer,
  "caption" text,
  "title" text,
  "exif" text,
  "ordering" integer,
  "ismainimage" smallint NOT NULL default '0',
  "ismainimageonly" smallint NOT NULL default '0',
  "hits" integer NOT NULL default '0',
  "published" smallint NOT NULL default '1',
  "allowcomments" smallint NOT NULL default '1',
  "userid" integer NOT NULL,
  "modified" timestamp without time zone NULL DEFAULT NULL,
  "created" timestamp without time zone NULL DEFAULT NULL,
  PRIMARY KEY  ("id"),
  CONSTRAINT "#__eventgallery_file_uc_folderfile" UNIQUE ("folder","file")
);
CREATE INDEX "#__eventgallery_file_idx_file" ON "#__eventgallery_file" ("file");
CREATE INDEX "#__eventgallery_file_idx_folder" ON "#__eventgallery_file" ("folder");


CREATE TABLE "#__eventgallery_folder" (
  "id" serial NOT NULL,
  "picasakey" varchar(125) DEFAULT NULL,
  "password" varchar(250) NOT NULL,
  "cartable" smallint NOT NULL DEFAULT  '1',
  "foldertags" text,
  "description" varchar(255) NOT NULL,
  "date" timestamp without time zone NOT NULL,
  "published" smallint NOT NULL DEFAULT '1',
  "folder" varchar(125) NOT NULL,
  "imagetypesetid" integer DEFAULT NULL,
  "watermarkid" integer DEFAULT NULL,
  "text" text,
  "hits" integer NOT NULL default '0',
  "userid" integer NOT NULL DEFAULT '0',
  "modified" timestamp without time zone NULL DEFAULT NULL,
  "created" timestamp without time zone NULL DEFAULT NULL,
  "ordering" integer NOT NULL DEFAULT '0',
  "usergroupids" text,
  "attribs" text,
  "catid" integer NOT NULL DEFAULT '0',
  PRIMARY KEY ("id"),
  CONSTRAINT "#__eventgallery_folder_uc_folder" UNIQUE ("folder")
);


DROP TABLE IF EXISTS "#__eventgallery_sequence";
CREATE TABLE "#__eventgallery_sequence" (
  "id" serial NOT NULL,
  "value" integer DEFAULT NULL,
   PRIMARY KEY ("id")
);
--
-- Tabellenstruktur für Tabelle "#__eventgallery_imagelineitem"
--
DROP TABLE IF EXISTS "#__eventgallery_imagelineitem";
CREATE TABLE "#__eventgallery_imagelineitem" (
  "id" serial NOT NULL,
  "folder" varchar(255) NOT NULL,
  "file" varchar(255) NOT NULL,
  "quantity" integer NOT NULL DEFAULT '1',
  "imagetypeid" integer DEFAULT NULL,
  "taxrate" NUMERIC(4,2)  DEFAULT 0
  "price" decimal(8,2) NOT NULL,
  "singleprice" decimal(8,2) NOT NULL,
  "currency" varchar(3) NOT NULL,
  "lineitemcontainerid" varchar(50) DEFAULT NULL,
  "modified" timestamp without time zone NULL DEFAULT NULL,
  "created" timestamp without time zone NULL DEFAULT NULL,  
  PRIMARY KEY ("id")
);
CREATE INDEX "#__eventgallery_file_idx_lineitemcontainerid" ON "#__eventgallery_imagelineitem" ("lineitemcontainerid");

--
-- Tabellenstruktur für Tabelle "#__eventgallery_servicelineitem"
--
DROP TABLE IF EXISTS "#__eventgallery_servicelineitem";
CREATE TABLE "#__eventgallery_servicelineitem" (
  "id" serial NOT NULL,
  "methodid" integer DEFAULT NULL,
  "lineitemcontainerid" varchar(50) DEFAULT NULL,
  "type" integer DEFAULT NULL,
  "quantity" integer NOT NULL DEFAULT '1',
  "name" varchar(45) DEFAULT NULL,
  "displayname" text DEFAULT NULL,
  "description" text DEFAULT NULL,
  "data" text DEFAULT NULL,
  "taxrate" NUMERIC(4,2)  DEFAULT 0
  "price" decimal(8,2) NOT NULL,
  "singleprice" decimal(8,2) NOT NULL,
  "currency" varchar(3) NOT NULL,
  "ordering" integer NOT NULL DEFAULT 0,
  "modified" timestamp without time zone NULL DEFAULT NULL,
  "created" timestamp without time zone NULL DEFAULT NULL,  
  PRIMARY KEY ("id")
);
CREATE INDEX "#__eventgallery_servicelineitem_idx_lineitemcontainerid" ON "#__eventgallery_servicelineitem" ("lineitemcontainerid");

-- --------------------------------------------------------
DROP TABLE IF EXISTS "#__eventgallery_imagetypeset";
CREATE TABLE "#__eventgallery_imagetypeset" (
  "id" serial NOT NULL,
  "name" varchar(45) DEFAULT NULL,
  "description" text DEFAULT NULL,
  "note" text DEFAULT NULL,
  "default" integer NOT NULL DEFAULT 0,
  "ordering" int NULL DEFAULT NULL,
  "published" integer NULL DEFAULT NULL,
  "modified" timestamp without time zone NULL DEFAULT NULL,
  "created" timestamp without time zone NULL DEFAULT NULL,

  PRIMARY KEY ("id")
);


DROP TABLE IF EXISTS "#__eventgallery_useraddress";
CREATE TABLE "#__eventgallery_useraddress" (
  "id" serial NOT NULL,
  "userid" varchar(45) NOT NULL,
  "firstname" varchar(255) DEFAULT NULL,
  "lastname" varchar(255) DEFAULT NULL,
  "address1" varchar(255) DEFAULT NULL,
  "address2" varchar(255) DEFAULT NULL,
  "address3" varchar(255) DEFAULT NULL,
  "city" varchar(255) DEFAULT NULL,
  "country" varchar(255) DEFAULT NULL,
  "zip" varchar(10) DEFAULT NULL,
  "default" integer NOT NULL DEFAULT 0,
  "modified" timestamp without time zone NULL DEFAULT NULL,
  "created" timestamp without time zone NULL DEFAULT NULL,

  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "#__eventgallery_staticaddress";
CREATE TABLE "#__eventgallery_staticaddress" (
  "id" serial NOT NULL,
  "firstname" varchar(255) DEFAULT NULL,
  "lastname" varchar(255) DEFAULT NULL,
  "address1" varchar(255) DEFAULT NULL,
  "address2" varchar(255) DEFAULT NULL,
  "address3" varchar(255) DEFAULT NULL,
  "city" varchar(255) DEFAULT NULL,
  "country" varchar(255) DEFAULT NULL,
  "zip" varchar(10) DEFAULT NULL,
  "valid" integer DEFAULT 0,
  "modified" timestamp without time zone NULL DEFAULT NULL,
  "created" timestamp without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "#__eventgallery_imagetypeset_imagetype_assignment";
CREATE TABLE "#__eventgallery_imagetypeset_imagetype_assignment" (
  "imagetypesetid" integer NOT NULL,
  "imagetypeid" integer NOT NULL,
  "default" integer NOT NULL DEFAULT 0,
  "ordering" integer NOT NULL DEFAULT 0,
  "modified" timestamp without time zone NULL DEFAULT NULL,
  "created" timestamp without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("imagetypesetid","imagetypeid")
);


--
-- Tabellenstruktur für Tabelle "#__eventgallery_imagetype"
--
DROP TABLE IF EXISTS "#__eventgallery_imagetype";
CREATE TABLE "#__eventgallery_imagetype" (
  "id" serial NOT NULL,
  "type" varchar(45) DEFAULT NULL,
  "isdigital" integer DEFAULT 0,
  "size" varchar(45) DEFAULT NULL,
  "taxrate" NUMERIC(4,2)  DEFAULT 0
  "price" decimal(8,2) DEFAULT NULL,
  "currency" varchar(3) NOT NULL,
  "name" varchar(255) DEFAULT NULL,
  "displayname" text DEFAULT NULL,
  "description" text DEFAULT NULL,
  "published" integer NULL DEFAULT NULL,
  "note" text DEFAULT NULL,
  "modified" timestamp without time zone NULL DEFAULT NULL,
  "created" timestamp without time zone NULL DEFAULT NULL,

  PRIMARY KEY ("id")
);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle "#__eventgallery_cart"
--
DROP TABLE IF EXISTS "#__eventgallery_cart";
CREATE TABLE "#__eventgallery_cart" (
  "id" varchar(50) NOT NULL ,
  "documentno" integer DEFAULT NULL,
  "userid" varchar(45) NOT NULL DEFAULT '',
  "email" varchar(255) NOT NULL DEFAULT '',
  "phone" varchar(255) NOT NULL DEFAULT '',
  "statusid" integer DEFAULT NULL,
  "subtotal" decimal(8,2) DEFAULT NULL,
  "subtotalcurrency" varchar(3) NOT NULL DEFAULT '',
  "total" decimal(8,2) DEFAULT NULL,
  "totalcurrency" varchar(3) NOT NULL DEFAULT '',
  "billingaddressid" integer DEFAULT NULL,
  "shippingaddressid" integer DEFAULT NULL,
  "message" text DEFAULT NULL,
  "modified" timestamp without time zone NULL DEFAULT NULL,
  "created" timestamp without time zone NULL DEFAULT NULL,

  PRIMARY KEY ("id")
);
CREATE INDEX "#__eventgallery_cart_idx_statusid" ON "#__eventgallery_cart" ("statusid");

DROP TABLE IF EXISTS "#__eventgallery_order";
CREATE TABLE "#__eventgallery_order" (
  "id" varchar(50) NOT NULL ,
  "documentno" varchar(45) DEFAULT NULL,
  "orderstatusid" integer DEFAULT NULL,
  "paymentstatusid" integer DEFAULT 0,
  "shippingstatusid" integer DEFAULT 0,
  "userid" varchar(45) NOT NULL,
  "email" varchar(255) NOT NULL,
  "phone" varchar(255) DEFAULT NULL,
  "subtotal" decimal(8,2) DEFAULT NULL,
  "subtotalcurrency" varchar(3) NOT NULL,
  "total" decimal(8,2) DEFAULT NULL,
  "totalcurrency" varchar(3) NOT NULL,
  "billingaddressid" integer DEFAULT NULL,
  "shippingaddressid" integer DEFAULT NULL,
  "message" text DEFAULT NULL,
  "modified" timestamp without time zone NULL DEFAULT NULL,
  "created" timestamp without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);
CREATE INDEX "#__eventgallery_order_idx_orderstatusid" ON "#__eventgallery_order" ("orderstatusid");

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle "#__eventgallery_orderstatus"
--
DROP TABLE IF EXISTS "#__eventgallery_orderstatus";
CREATE TABLE "#__eventgallery_orderstatus" (
  "id" serial NOT NULL,
  "name" varchar(255) DEFAULT NULL,
  "default" integer NOT NULL DEFAULT 0,
  "systemmanaged" integer NOT NULL DEFAULT 0,
  "type" integer NOT NULL DEFAULT 0,
  "ordering" integer NOT NULL DEFAULT 0,
  "displayname" text DEFAULT NULL,
  "description" text DEFAULT NULL,
  "modified" timestamp without time zone NULL DEFAULT NULL,
  "created" timestamp without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle "#__eventgallery_paymentmethod"
--
DROP TABLE IF EXISTS "#__eventgallery_paymentmethod";
CREATE TABLE "#__eventgallery_paymentmethod" (
  "id" serial NOT NULL,
  "name" varchar(45) DEFAULT NULL,
  "displayname" text DEFAULT NULL,
  "description" text DEFAULT NULL,
  "data" text DEFAULT NULL,
  "classname" varchar(255) DEFAULT NULL,
  "taxrate" NUMERIC(4,2)  DEFAULT 0
  "price" decimal(8,2) NOT NULL,
  "currency" varchar(3) NOT NULL,
  "published" smallint NOT NULL DEFAULT '0',
  "default" integer NOT NULL DEFAULT 0,
  "ordering" integer NOT NULL DEFAULT 0,
  "modified" timestamp without time zone NULL DEFAULT NULL,
  "created" timestamp without time zone NULL DEFAULT NULL,  
  PRIMARY KEY ("id")
);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle "#__eventgallery_shippingmethod"
--
DROP TABLE IF EXISTS "#__eventgallery_shippingmethod";
CREATE TABLE "#__eventgallery_shippingmethod" (
  "id" serial NOT NULL,
  "name" varchar(45) DEFAULT NULL,
  "supportsdigital" integer DEFAULT 0,
  "displayname" text DEFAULT NULL,
  "description" text DEFAULT NULL,
  "data" text DEFAULT NULL,
  "classname" varchar(255) DEFAULT NULL,
  "taxrate" NUMERIC(4,2)  DEFAULT 0
  "price" decimal(8,2) NOT NULL,
  "currency" varchar(3) NOT NULL,
  "published" smallint NOT NULL DEFAULT '0',
  "default" integer NOT NULL DEFAULT 0,
  "ordering" integer NOT NULL DEFAULT 0,
  "modified" timestamp without time zone NULL DEFAULT NULL,
  "created" timestamp without time zone NULL DEFAULT NULL,
  
  PRIMARY KEY ("id")
);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle "#__eventgallery_surcharge"
--
DROP TABLE IF EXISTS "#__eventgallery_surcharge";
CREATE TABLE "#__eventgallery_surcharge" (
  "id" serial NOT NULL,
  "name" varchar(45) DEFAULT NULL,
  "displayname" text DEFAULT NULL,
  "description" text DEFAULT NULL,
  "data" text DEFAULT NULL, 
  "classname" varchar(255) DEFAULT NULL,
  "taxrate" NUMERIC(4,2)  DEFAULT 0
  "price" decimal(8,2) NOT NULL,
  "currency" varchar(3) NOT NULL,
  "published" smallint NOT NULL DEFAULT '0',
  "ordering" integer NOT NULL DEFAULT 0,
  "default" integer NOT NULL DEFAULT 0,
  "rule" integer DEFAULT NULL,
  "modified" timestamp without time zone NULL DEFAULT NULL,
  "created" timestamp without time zone NULL DEFAULT NULL,
  
  PRIMARY KEY ("id")
);


--
-- Tabellenstruktur für Tabelle `#__eventgallery_watermark`
--
DROP TABLE IF EXISTS `#__eventgallery_watermark`;
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



--
-- Daten für Tabelle "#__eventgallery_imagetype"
--

INSERT INTO "#__eventgallery_imagetype" ("id", "published", "type", "isdigital", "size", "taxrate", "price", "currency", "name", "displayname", "description", "note", "modified", "created") VALUES
(1, 1, 'paper', 0, '13x18', 19, 0.70, 'EUR', 'Fotoabzug 13x18', '{"en-GB":"Print 5x7","de-DE":"Foto 13x18"}', '{"en-GB":"A print with the size of 5x7 on premium photo paper","de-DE":"Ein Abzug der Größe 13x18 auf Premium-Fotopapier"}', 'I''ll order this using Pixum.', now(), NULL),
(2, 1, 'paper', 0, '10x15', 19, 0.90, 'EUR', 'Fotoabzug 10x15', '{"en-GB":"Print 4x5","de-DE":"Foto 11x13"}', '{"en-GB":"A print with the size of 4x5 on premium photo paper","de-DE":"Ein Abzug der Größe 11x13 auf Premium-Fotopapier"}', 'I''ll order this using Pixum', NULL, NULL),
(3, 1, 'digital', 1, '20 MP', 19, 12.40, 'EUR', 'Digitale Kopie', '{"en-GB":"Digital Copy","de-DE":"Digitale Kopie"}', '{"en-GB":"A digital copy of the original image","de-DE":"Eine Kopie des originalen Bildes."}', 'Copy from my hard drive', now(), NULL),
(4, 1, 'paper', 0, '13x18', 19, 2.00, 'EUR', 'Fotoabzug Premium 13x18', '{"en-GB":"Premium Print 5x7","de-DE":"Premium Foto 13x18"}', '{"en-GB":"A print with the size of 5x7 on premium photo paper","de-DE":"Ein Abzug der Größe 13x18 auf Premium-Fotopapier"}', 'I''ll order this using Pixum.', now(), NULL),
(5, 1, 'paper', 0, '10x15', 19, 2.50, 'EUR', 'Fotoabzug 10x15', '{"en-GB":"Premium Print 4x5","de-DE":"Foto 11x13"}', '{"en-GB":"A print with the size of 4x5 on premium photo paper","de-DE":"Ein Abzug der Größe 11x13 auf Premium-Fotopapier"}', 'I''ll order this using Pixum', NULL, NULL),
(6, 1, 'digital exp', 1, '20 MP', 19, 25.00, 'EUR', 'Digitale Kopie', '{"en-GB":"Digital Copy","de-DE":"Digitale Kopie"}', '{"en-GB":"A digital copy of the original image","de-DE":"Eine Kopie des originalen Bildes."}', 'Copy from my hard drive', now(), NULL);
--
-- Daten für Tabelle "#__eventgallery_imagetypeset"
--

INSERT INTO "#__eventgallery_imagetypeset"  ("id", "name", "description", "note", "default", "published", "modified", "created") VALUES
(1, 'Cheap images', NULL, NULL, 0, 1, now(), NULL),
(2, 'Expensive images', NULL, NULL, 1, 1, NULL, NULL);

--
-- Daten für Tabelle "#__eventgallery_imagetypeset_imagetype_assignment"
--

INSERT INTO "#__eventgallery_imagetypeset_imagetype_assignment" ("imagetypesetid", "imagetypeid", "default", "ordering", "modified", "created") VALUES
(1, 1, 0, 1, now(), NULL),
(1, 2, 1, 2, NULL, NULL),
(1, 3, 0, 4, now(), NULL),
(1, 4, 0, 3, now(), NULL),
(2, 4, 0, 1, now(), NULL),
(2, 5, 0, 2, NULL, NULL),
(2, 6, 1, 3, now(), NULL);

--
-- Daten für Tabelle "#__eventgallery_paymentmethod"
--

INSERT INTO "#__eventgallery_paymentmethod" ("id", "classname", "name", "displayname", "description", "taxrate", "price", "currency", "published", "default", "ordering", "modified", "created", "data") VALUES
(1, 'EventgalleryPluginsPaymentStandard', 'Cash on Pickup', '{"en-GB":"Cash on pickup","de-DE":"Zahlung bei Abholung"}', '{"en-GB":"Pay when you pick up your order","de-DE":"Die Bezahlung erfolgt bei Abholung"}', 19, 0.00, 'EUR', '1', '0', '1', now(), NULL, ''),
(2, 'EventgalleryPluginsPaymentStandard', 'COD', '{"en-GB":"Cash on Delivery","de-DE":"Nachnahme"}', '{"en-GB":"Pay per Cash on Delivery","de-DE":"Zahlung per Nachnahme"}', 19, 2.00, 'EUR', '1','0', '2',  now(), NULL, '');

--
-- Daten für Tabelle "#__eventgallery_shippingmethod"
--

INSERT INTO "#__eventgallery_shippingmethod" ("id", "classname", "name", "displayname", "description", "taxrate", "price", "currency", "published", "default", "ordering", "modified", "created") VALUES
(3, 'EventgalleryPluginsShippingStandard', 'ground', '{"en-GB":"Mail","de-DE":"Post"}', '{"en-GB":"Shipping of your items in a parcel","de-DE":"Versand mit Post"}', 19, 6.00, 'EUR','1', '0', '3',  now(), NULL);

--
-- Daten für Tabelle "#__eventgallery_orderstatus"
--

INSERT INTO "#__eventgallery_orderstatus" ("id", "ordering", "type", "systemmanaged", "name", "default", "displayname", "description", "modified", "created") VALUES
(1, 1, '0', '0', 'new', 1, '{"en-GB":"New","de-DE":"Neu"}', '{"en-GB":"New","de-DE":"Neu"}', now(), NULL),
(2, 2, '0', '0', 'refused', 0, '{"en-GB":"Refused","de-DE":"Abgelehnt"}', '{"en-GB":"Refused by merchant","de-DE":"Vom Anbieter abgelehnt"}', NULL, NULL),
(3, 3, '0', '0', 'canceled', 0, '{"en-GB":"Canceled","de-DE":"Storniert"}', '{"en-GB":"Canceled by customer","de-DE":"Durch Nutzer storniert"}', now(), NULL),
(4, 4, '0', '0', 'in progress', 0, '{"en-GB":"In progress","de-DE":"In Bearbeitung"}', '{"en-GB":"In progress","de-DE":"In Bearbeitung"}', NULL, NULL),
(5, 5, '0', '0', 'completed', 0, '{"en-GB":"Completed","de-DE":"Abgeschlossen"}', '{"en-GB":"Order is completed","de-DE":"Die Bestellung ist abgeschlossen."}', now(), NULL),
(6, 6, '1', '1', 'not shipped', 1, '{"en-GB":"Not Shipped","de-DE":"Noch nicht versendet"}', '{"en-GB":"Shipping of the order id pending.","de-DE":"Die Bestellung wurde noch nicht verschickt."}', NULL, NULL),
(7, 7, '1', '1', 'shipped', 0, '{"en-GB":"Shipped","de-DE":"Versendet"}', '{"en-GB":"Die Bestellung wurde versendet.","de-DE":"Die Bestellung wurde versandt."}', NULL, NULL),
(8, 8, '2', '1', 'not payed', 1, '{"en-GB":"Not payed","de-DE":"Nicht bezahlt"}', '{"en-GB":"The order is not payed yet.","de-DE":"Die Bestellung wurde noch nicht bezahlt"}', NULL, NULL),
(9, 9, '2', '1', 'payed', 0, '{"en-GB":"Payed","de-DE":"Bezahlt"}', '{"en-GB":"The order is payed.","de-DE":"Die Bestellung wurde bezahlt."}', NULL, NULL);



