
-- Walrus Translation Software
-- 
-- Copyright (c) Neil Zanella. All rigts reserved.

------------------------------------------------------------
-- Internationalization database tables: connect with:    --
-- $ mysql -h host.domain -u username -psecret dbname     --
------------------------------------------------------------

DROP TABLE MaintainedTranslations;
DROP TABLE Translations;
DROP TABLE SubmittedTranslations;
DROP TABLE WorkingTranslations;
DROP TABLE Translators;
DROP TABLE Source;
DROP TABLE Languages;
DROP TABLE Countries;
DROP TABLE Mentions;

------------------------------------------------------------
-- Create ISO 639 two-letter language code table.        --
------------------------------------------------------------

CREATE TABLE Languages (
  lcode CHAR(2) NOT NULL,
  lang VARCHAR(16) NOT NULL,
  UNIQUE (lang),
  PRIMARY KEY (lcode)
);

------------------------------------------------------------
-- Create ISO 3166 two-letter country code table.        --
------------------------------------------------------------

CREATE TABLE Countries (
  ccode CHAR(2) NOT NULL,
  country VARCHAR(48) NOT NULL,
  UNIQUE (country),
  PRIMARY KEY (ccode)
);

------------------------------------------------------------
-- Create Mentions table.                                 --
------------------------------------------------------------

CREATE TABLE Mentions (
  mentionid INTEGER NOT NULL AUTO_INCREMENT,
  mention VARCHAR(120) NOT NULL,
  PRIMARY KEY (mentionid)
);

------------------------------------------------------------
-- Create Translators table.                              --
------------------------------------------------------------

CREATE TABLE Translators (
  username VARCHAR(60) NOT NULL,
  realname VARCHAR(60) NOT NULL,
  email VARCHAR(60) NOT NULL,
  password VARCHAR(16) NOT NULL,
  PRIMARY KEY (username)
);

------------------------------------------------------------
-- Create table to contain source text to be translated.  --
------------------------------------------------------------

CREATE TABLE Source (
  textid INTEGER NOT NULL AUTO_INCREMENT,
  text VARCHAR(120) NOT NULL,
  PRIMARY KEY (textid)
);

------------------------------------------------------------
-- Create table to contain translated text.               --
------------------------------------------------------------

CREATE TABLE WorkingTranslations (
  username VARCHAR(40) NOT NULL,
  lcode CHAR(2) NOT NULL,
  ccode CHAR(2) NOT NULL DEFAULT '--',
  textid VARCHAR(120) NOT NULL,
  tran VARCHAR(120) NOT NULL,
  PRIMARY KEY (username, lcode, ccode, textid),
  FOREIGN KEY (username) REFERENCES Translators,
  FOREIGN KEY (lcode) REFERENCES Languages,
  FOREIGN KEY (ccode) REFERENCES Countries,
  FOREIGN KEY (textid) REFERENCES Source
);

------------------------------------------------------------
-- Create table to contain submitted translated text.     --
------------------------------------------------------------

CREATE TABLE SubmittedTranslations (
  username VARCHAR(40) NOT NULL,
  lcode CHAR(2) NOT NULL,
  ccode CHAR(2) NOT NULL,
  textid VARCHAR(120) NOT NULL,
  tran VARCHAR(120) NOT NULL,
  PRIMARY KEY (username, lcode, ccode, textid),
  FOREIGN KEY (username) REFERENCES Translators,
  FOREIGN KEY (lcode) REFERENCES Languages,
  FOREIGN KEY (ccode) REFERENCES Countries,
  FOREIGN KEY (textid) REFERENCES Source
);

------------------------------------------------------------
-- Create SubmittedMaintainersMentions table.             --
------------------------------------------------------------

CREATE TABLE Translations (
  username VARCHAR(60) NOT NULL,
  lcode CHAR(2) NOT NULL,
  ccode CHAR(2) NOT NULL,
  mentionid INTEGER,
  PRIMARY KEY (username, lcode, ccode),
  FOREIGN KEY (username) REFERENCES Translators,
  FOREIGN KEY (lcode) REFERENCES Languages,
  FOREIGN KEY (ccode) REFERENCES Countries
);

------------------------------------------------------------
-- Create Maintainers table.                              --
------------------------------------------------------------

CREATE TABLE MaintainedTranslations (
  username VARCHAR(60) NOT NULL,
  lcode CHAR(2) NOT NULL,
  ccode CHAR(2) NOT NULL,
  PRIMARY KEY (lcode, ccode),
  FOREIGN KEY (username, lcode, ccode) REFERENCES Translations
);

------------------------------------------------------------
-- Populate Languages table with ISO 639 data.            --
------------------------------------------------------------

INSERT INTO Languages (lcode, lang) VALUES ('--', '-- select one --');
INSERT INTO Languages (lcode, lang) VALUES ('aa', 'Afar');
INSERT INTO Languages (lcode, lang) VALUES ('ab', 'Abkhazian');
INSERT INTO Languages (lcode, lang) VALUES ('af', 'Afrikaans');
INSERT INTO Languages (lcode, lang) VALUES ('am', 'Amharic');
INSERT INTO Languages (lcode, lang) VALUES ('ar', 'Arabic');
INSERT INTO Languages (lcode, lang) VALUES ('as', 'Assamese');
INSERT INTO Languages (lcode, lang) VALUES ('ay', 'Aymara');
INSERT INTO Languages (lcode, lang) VALUES ('az', 'Azerbaijani');
INSERT INTO Languages (lcode, lang) VALUES ('ba', 'Bashkir');
INSERT INTO Languages (lcode, lang) VALUES ('be', 'Byelorussian');
INSERT INTO Languages (lcode, lang) VALUES ('bg', 'Bulgarian');
INSERT INTO Languages (lcode, lang) VALUES ('bh', 'Bihari');
INSERT INTO Languages (lcode, lang) VALUES ('bi', 'Bislama');
INSERT INTO Languages (lcode, lang) VALUES ('bn', 'Bengali');
INSERT INTO Languages (lcode, lang) VALUES ('bo', 'Tibetan');
INSERT INTO Languages (lcode, lang) VALUES ('br', 'Breton');
INSERT INTO Languages (lcode, lang) VALUES ('ca', 'Catalan');
INSERT INTO Languages (lcode, lang) VALUES ('co', 'Corsican');
INSERT INTO Languages (lcode, lang) VALUES ('cs', 'Czech');
INSERT INTO Languages (lcode, lang) VALUES ('cy', 'Welsh');
INSERT INTO Languages (lcode, lang) VALUES ('da', 'Danish');
INSERT INTO Languages (lcode, lang) VALUES ('de', 'German');
INSERT INTO Languages (lcode, lang) VALUES ('dz', 'Bhutani');
INSERT INTO Languages (lcode, lang) VALUES ('el', 'Greek');
INSERT INTO Languages (lcode, lang) VALUES ('en', 'English');
INSERT INTO Languages (lcode, lang) VALUES ('eo', 'Esperanto');
INSERT INTO Languages (lcode, lang) VALUES ('es', 'Spanish');
INSERT INTO Languages (lcode, lang) VALUES ('et', 'Estonian');
INSERT INTO Languages (lcode, lang) VALUES ('eu', 'Basque');
INSERT INTO Languages (lcode, lang) VALUES ('fa', 'Persian');
INSERT INTO Languages (lcode, lang) VALUES ('fi', 'Finnish');
INSERT INTO Languages (lcode, lang) VALUES ('fj', 'Fiji');
INSERT INTO Languages (lcode, lang) VALUES ('fo', 'Faeroese');
INSERT INTO Languages (lcode, lang) VALUES ('fr', 'French');
INSERT INTO Languages (lcode, lang) VALUES ('fy', 'Frisian');
INSERT INTO Languages (lcode, lang) VALUES ('ga', 'Irish');
INSERT INTO Languages (lcode, lang) VALUES ('gd', 'Gaelic');
INSERT INTO Languages (lcode, lang) VALUES ('gl', 'Galician');
INSERT INTO Languages (lcode, lang) VALUES ('gn', 'Guarani');
INSERT INTO Languages (lcode, lang) VALUES ('gu', 'Gujarati');
INSERT INTO Languages (lcode, lang) VALUES ('ha', 'Hausa');
INSERT INTO Languages (lcode, lang) VALUES ('hi', 'Hindi');
INSERT INTO Languages (lcode, lang) VALUES ('hr', 'Croatian');
INSERT INTO Languages (lcode, lang) VALUES ('hu', 'Hungarian');
INSERT INTO Languages (lcode, lang) VALUES ('hy', 'Armenian');
INSERT INTO Languages (lcode, lang) VALUES ('ia', 'Interlingua');
INSERT INTO Languages (lcode, lang) VALUES ('ie', 'Interlingue');
INSERT INTO Languages (lcode, lang) VALUES ('ik', 'Inupiak');
INSERT INTO Languages (lcode, lang) VALUES ('in', 'Indonesian');
INSERT INTO Languages (lcode, lang) VALUES ('is', 'Icelandic');
INSERT INTO Languages (lcode, lang) VALUES ('it', 'Italian');
INSERT INTO Languages (lcode, lang) VALUES ('iw', 'Hebrew');
INSERT INTO Languages (lcode, lang) VALUES ('ja', 'Japanese');
INSERT INTO Languages (lcode, lang) VALUES ('ji', 'Yiddish');
INSERT INTO Languages (lcode, lang) VALUES ('jw', 'Javanese');
INSERT INTO Languages (lcode, lang) VALUES ('ka', 'Georgian');
INSERT INTO Languages (lcode, lang) VALUES ('kk', 'Kazakh');
INSERT INTO Languages (lcode, lang) VALUES ('kl', 'Greenlandic');
INSERT INTO Languages (lcode, lang) VALUES ('km', 'Cambodian');
INSERT INTO Languages (lcode, lang) VALUES ('kn', 'Kannada');
INSERT INTO Languages (lcode, lang) VALUES ('ko', 'Korean');
INSERT INTO Languages (lcode, lang) VALUES ('ks', 'Kashmiri');
INSERT INTO Languages (lcode, lang) VALUES ('ku', 'Kurdish');
INSERT INTO Languages (lcode, lang) VALUES ('ky', 'Kirghiz');
INSERT INTO Languages (lcode, lang) VALUES ('la', 'Latin');
INSERT INTO Languages (lcode, lang) VALUES ('ln', 'Lingala');
INSERT INTO Languages (lcode, lang) VALUES ('lo', 'Laothian');
INSERT INTO Languages (lcode, lang) VALUES ('lt', 'Lithuanian');
INSERT INTO Languages (lcode, lang) VALUES ('lv', 'Latvian');
INSERT INTO Languages (lcode, lang) VALUES ('mg', 'Malagasy');
INSERT INTO Languages (lcode, lang) VALUES ('mi', 'Maori');
INSERT INTO Languages (lcode, lang) VALUES ('mk', 'Macedonian');
INSERT INTO Languages (lcode, lang) VALUES ('ml', 'Malayalam');
INSERT INTO Languages (lcode, lang) VALUES ('mn', 'Mongolian');
INSERT INTO Languages (lcode, lang) VALUES ('mo', 'Moldavian');
INSERT INTO Languages (lcode, lang) VALUES ('mr', 'Marathi');
INSERT INTO Languages (lcode, lang) VALUES ('ms', 'Malay');
INSERT INTO Languages (lcode, lang) VALUES ('mt', 'Maltese');
INSERT INTO Languages (lcode, lang) VALUES ('my', 'Burmese');
INSERT INTO Languages (lcode, lang) VALUES ('na', 'Nauru');
INSERT INTO Languages (lcode, lang) VALUES ('ne', 'Nepali');
INSERT INTO Languages (lcode, lang) VALUES ('nl', 'Dutch');
INSERT INTO Languages (lcode, lang) VALUES ('no', 'Norwegian');
INSERT INTO Languages (lcode, lang) VALUES ('oc', 'Occitan');
INSERT INTO Languages (lcode, lang) VALUES ('om', 'Oromo');
INSERT INTO Languages (lcode, lang) VALUES ('or', 'Oriya');
INSERT INTO Languages (lcode, lang) VALUES ('pa', 'Punjabi');
INSERT INTO Languages (lcode, lang) VALUES ('pl', 'Polish');
INSERT INTO Languages (lcode, lang) VALUES ('ps', 'Pashto');
INSERT INTO Languages (lcode, lang) VALUES ('pt', 'Portuguese');
INSERT INTO Languages (lcode, lang) VALUES ('qu', 'Quechua');
INSERT INTO Languages (lcode, lang) VALUES ('rm', 'Rhaeto-Romance');
INSERT INTO Languages (lcode, lang) VALUES ('rn', 'Kirundi');
INSERT INTO Languages (lcode, lang) VALUES ('ro', 'Romanian');
INSERT INTO Languages (lcode, lang) VALUES ('ru', 'Russian');
INSERT INTO Languages (lcode, lang) VALUES ('rw', 'Kinyarwanda');
INSERT INTO Languages (lcode, lang) VALUES ('sa', 'Sanskrit');
INSERT INTO Languages (lcode, lang) VALUES ('sd', 'Sindhi');
INSERT INTO Languages (lcode, lang) VALUES ('sg', 'Sangro');
INSERT INTO Languages (lcode, lang) VALUES ('sh', 'Serbo-Croatian');
INSERT INTO Languages (lcode, lang) VALUES ('si', 'Singhalese');
INSERT INTO Languages (lcode, lang) VALUES ('sk', 'Slovak');
INSERT INTO Languages (lcode, lang) VALUES ('sl', 'Slovenian');
INSERT INTO Languages (lcode, lang) VALUES ('sm', 'Samoan');
INSERT INTO Languages (lcode, lang) VALUES ('sn', 'Shona');
INSERT INTO Languages (lcode, lang) VALUES ('so', 'Somali');
INSERT INTO Languages (lcode, lang) VALUES ('sq', 'Albanian');
INSERT INTO Languages (lcode, lang) VALUES ('sr', 'Serbian');
INSERT INTO Languages (lcode, lang) VALUES ('ss', 'Siswati');
INSERT INTO Languages (lcode, lang) VALUES ('st', 'Sesotho');
INSERT INTO Languages (lcode, lang) VALUES ('su', 'Sudanese');
INSERT INTO Languages (lcode, lang) VALUES ('sv', 'Swedish');
INSERT INTO Languages (lcode, lang) VALUES ('sw', 'Swahili');
INSERT INTO Languages (lcode, lang) VALUES ('ta', 'Tamil');
INSERT INTO Languages (lcode, lang) VALUES ('te', 'Tegulu');
INSERT INTO Languages (lcode, lang) VALUES ('tg', 'Tajik');
INSERT INTO Languages (lcode, lang) VALUES ('th', 'Thai');
INSERT INTO Languages (lcode, lang) VALUES ('ti', 'Tigrinya');
INSERT INTO Languages (lcode, lang) VALUES ('tk', 'Turkmen');
INSERT INTO Languages (lcode, lang) VALUES ('tl', 'Tagalog');
INSERT INTO Languages (lcode, lang) VALUES ('tn', 'Setswana');
INSERT INTO Languages (lcode, lang) VALUES ('to', 'Tonga');
INSERT INTO Languages (lcode, lang) VALUES ('tr', 'Turkish');
INSERT INTO Languages (lcode, lang) VALUES ('ts', 'Tsonga');
INSERT INTO Languages (lcode, lang) VALUES ('tt', 'Tatar');
INSERT INTO Languages (lcode, lang) VALUES ('tw', 'Twi');
INSERT INTO Languages (lcode, lang) VALUES ('uk', 'Ukrainian');
INSERT INTO Languages (lcode, lang) VALUES ('ur', 'Urdu');
INSERT INTO Languages (lcode, lang) VALUES ('uz', 'Uzbek');
INSERT INTO Languages (lcode, lang) VALUES ('vi', 'Vietnamese');
INSERT INTO Languages (lcode, lang) VALUES ('vo', 'Volapuk');
INSERT INTO Languages (lcode, lang) VALUES ('wo', 'Wolof');
INSERT INTO Languages (lcode, lang) VALUES ('xh', 'Xhosa');
INSERT INTO Languages (lcode, lang) VALUES ('yo', 'Yoruba');
INSERT INTO Languages (lcode, lang) VALUES ('zh', 'Chinese');
INSERT INTO Languages (lcode, lang) VALUES ('zu', 'Zulu');

------------------------------------------------------------
-- Populate Countries table with ISO 3166 data.           --
------------------------------------------------------------

INSERT INTO Countries (ccode, country) VALUES ('--', '-- omitted --');
INSERT INTO Countries (ccode, country) VALUES ('AD', 'ANDORRA');
INSERT INTO Countries (ccode, country) VALUES ('AE', 'UNITED ARAB EMIRATES');
INSERT INTO Countries (ccode, country) VALUES ('AF', 'AFGHANISTAN');
INSERT INTO Countries (ccode, country) VALUES ('AG', 'ANTIGUA AND BARBUDA');
INSERT INTO Countries (ccode, country) VALUES ('AI', 'ANGUILLA');
INSERT INTO Countries (ccode, country) VALUES ('AL', 'ALBANIA');
INSERT INTO Countries (ccode, country) VALUES ('AM', 'ARMENIA');
INSERT INTO Countries (ccode, country) VALUES ('AN', 'NETHERLANDS ANTILLES');
INSERT INTO Countries (ccode, country) VALUES ('AO', 'ANGOLA');
INSERT INTO Countries (ccode, country) VALUES ('AQ', 'ANTARCTICA');
INSERT INTO Countries (ccode, country) VALUES ('AR', 'ARGENTINA');
INSERT INTO Countries (ccode, country) VALUES ('AS', 'AMERICAN SAMOA');
INSERT INTO Countries (ccode, country) VALUES ('AT', 'AUSTRIA');
INSERT INTO Countries (ccode, country) VALUES ('AU', 'AUSTRALIA');
INSERT INTO Countries (ccode, country) VALUES ('AW', 'ARUBA');
INSERT INTO Countries (ccode, country) VALUES ('AX', 'ÅLAND ISLANDS');
INSERT INTO Countries (ccode, country) VALUES ('AZ', 'AZERBAIJAN');
INSERT INTO Countries (ccode, country) VALUES ('BA', 'BOSNIA AND HERZEGOVINA');
INSERT INTO Countries (ccode, country) VALUES ('BB', 'BARBADOS');
INSERT INTO Countries (ccode, country) VALUES ('BD', 'BANGLADESH');
INSERT INTO Countries (ccode, country) VALUES ('BE', 'BELGIUM');
INSERT INTO Countries (ccode, country) VALUES ('BF', 'BURKINA FASO');
INSERT INTO Countries (ccode, country) VALUES ('BG', 'BULGARIA');
INSERT INTO Countries (ccode, country) VALUES ('BH', 'BAHRAIN');
INSERT INTO Countries (ccode, country) VALUES ('BI', 'BURUNDI');
INSERT INTO Countries (ccode, country) VALUES ('BJ', 'BENIN');
INSERT INTO Countries (ccode, country) VALUES ('BM', 'BERMUDA');
INSERT INTO Countries (ccode, country) VALUES ('BN', 'BRUNEI DARUSSALAM');
INSERT INTO Countries (ccode, country) VALUES ('BO', 'BOLIVIA');
INSERT INTO Countries (ccode, country) VALUES ('BR', 'BRAZIL');
INSERT INTO Countries (ccode, country) VALUES ('BS', 'BAHAMAS');
INSERT INTO Countries (ccode, country) VALUES ('BT', 'BHUTAN');
INSERT INTO Countries (ccode, country) VALUES ('BV', 'BOUVET ISLAND');
INSERT INTO Countries (ccode, country) VALUES ('BW', 'BOTSWANA');
INSERT INTO Countries (ccode, country) VALUES ('BY', 'BELARUS');
INSERT INTO Countries (ccode, country) VALUES ('BZ', 'BELIZE');
INSERT INTO Countries (ccode, country) VALUES ('CA', 'CANADA');
INSERT INTO Countries (ccode, country) VALUES ('CC', 'COCOS (KEELING) ISLANDS');
INSERT INTO Countries (ccode, country) VALUES ('CD', 'CONGO, THE DEMOCRATIC REPUBLIC OF THE');
INSERT INTO Countries (ccode, country) VALUES ('CF', 'CENTRAL AFRICAN REPUBLIC');
INSERT INTO Countries (ccode, country) VALUES ('CG', 'CONGO');
INSERT INTO Countries (ccode, country) VALUES ('CH', 'SWITZERLAND');
INSERT INTO Countries (ccode, country) VALUES ('CI', 'CÔTE D''IVOIRE');
INSERT INTO Countries (ccode, country) VALUES ('CK', 'COOK ISLANDS');
INSERT INTO Countries (ccode, country) VALUES ('CL', 'CHILE');
INSERT INTO Countries (ccode, country) VALUES ('CM', 'CAMEROON');
INSERT INTO Countries (ccode, country) VALUES ('CN', 'CHINA');
INSERT INTO Countries (ccode, country) VALUES ('CO', 'COLOMBIA');
INSERT INTO Countries (ccode, country) VALUES ('CR', 'COSTA RICA');
INSERT INTO Countries (ccode, country) VALUES ('CS', 'SERBIA AND MONTENEGRO');
INSERT INTO Countries (ccode, country) VALUES ('CU', 'CUBA');
INSERT INTO Countries (ccode, country) VALUES ('CV', 'CAPE VERDE');
INSERT INTO Countries (ccode, country) VALUES ('CX', 'CHRISTMAS ISLAND');
INSERT INTO Countries (ccode, country) VALUES ('CY', 'CYPRUS');
INSERT INTO Countries (ccode, country) VALUES ('CZ', 'CZECH REPUBLIC');
INSERT INTO Countries (ccode, country) VALUES ('DE', 'GERMANY');
INSERT INTO Countries (ccode, country) VALUES ('DJ', 'DJIBOUTI');
INSERT INTO Countries (ccode, country) VALUES ('DK', 'DENMARK');
INSERT INTO Countries (ccode, country) VALUES ('DM', 'DOMINICA');
INSERT INTO Countries (ccode, country) VALUES ('DO', 'DOMINICAN REPUBLIC');
INSERT INTO Countries (ccode, country) VALUES ('DZ', 'ALGERIA');
INSERT INTO Countries (ccode, country) VALUES ('EC', 'ECUADOR');
INSERT INTO Countries (ccode, country) VALUES ('EE', 'ESTONIA');
INSERT INTO Countries (ccode, country) VALUES ('EG', 'EGYPT');
INSERT INTO Countries (ccode, country) VALUES ('EH', 'WESTERN SAHARA');
INSERT INTO Countries (ccode, country) VALUES ('ER', 'ERITREA');
INSERT INTO Countries (ccode, country) VALUES ('ES', 'SPAIN');
INSERT INTO Countries (ccode, country) VALUES ('ET', 'ETHIOPIA');
INSERT INTO Countries (ccode, country) VALUES ('FI', 'FINLAND');
INSERT INTO Countries (ccode, country) VALUES ('FJ', 'FIJI');
INSERT INTO Countries (ccode, country) VALUES ('FK', 'FALKLAND ISLANDS (MALVINAS)');
INSERT INTO Countries (ccode, country) VALUES ('FM', 'MICRONESIA, FEDERATED STATES OF');
INSERT INTO Countries (ccode, country) VALUES ('FO', 'FAROE ISLANDS');
INSERT INTO Countries (ccode, country) VALUES ('FR', 'FRANCE');
INSERT INTO Countries (ccode, country) VALUES ('GA', 'GABON');
INSERT INTO Countries (ccode, country) VALUES ('GB', 'UNITED KINGDOM');
INSERT INTO Countries (ccode, country) VALUES ('GD', 'GRENADA');
INSERT INTO Countries (ccode, country) VALUES ('GE', 'GEORGIA');
INSERT INTO Countries (ccode, country) VALUES ('GF', 'FRENCH GUIANA');
INSERT INTO Countries (ccode, country) VALUES ('GH', 'GHANA');
INSERT INTO Countries (ccode, country) VALUES ('GI', 'GIBRALTAR');
INSERT INTO Countries (ccode, country) VALUES ('GL', 'GREENLAND');
INSERT INTO Countries (ccode, country) VALUES ('GM', 'GAMBIA');
INSERT INTO Countries (ccode, country) VALUES ('GN', 'GUINEA');
INSERT INTO Countries (ccode, country) VALUES ('GP', 'GUADELOUPE');
INSERT INTO Countries (ccode, country) VALUES ('GQ', 'EQUATORIAL GUINEA');
INSERT INTO Countries (ccode, country) VALUES ('GR', 'GREECE');
INSERT INTO Countries (ccode, country) VALUES ('GS', 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS');
INSERT INTO Countries (ccode, country) VALUES ('GT', 'GUATEMALA');
INSERT INTO Countries (ccode, country) VALUES ('GU', 'GUAM');
INSERT INTO Countries (ccode, country) VALUES ('GW', 'GUINEA-BISSAU');
INSERT INTO Countries (ccode, country) VALUES ('GY', 'GUYANA');
INSERT INTO Countries (ccode, country) VALUES ('HK', 'HONG KONG');
INSERT INTO Countries (ccode, country) VALUES ('HM', 'HEARD ISLAND AND MCDONALD ISLANDS');
INSERT INTO Countries (ccode, country) VALUES ('HN', 'HONDURAS');
INSERT INTO Countries (ccode, country) VALUES ('HR', 'CROATIA');
INSERT INTO Countries (ccode, country) VALUES ('HT', 'HAITI');
INSERT INTO Countries (ccode, country) VALUES ('HU', 'HUNGARY');
INSERT INTO Countries (ccode, country) VALUES ('ID', 'INDONESIA');
INSERT INTO Countries (ccode, country) VALUES ('IE', 'IRELAND');
INSERT INTO Countries (ccode, country) VALUES ('IL', 'ISRAEL');
INSERT INTO Countries (ccode, country) VALUES ('IN', 'INDIA');
INSERT INTO Countries (ccode, country) VALUES ('IO', 'BRITISH INDIAN OCEAN TERRITORY');
INSERT INTO Countries (ccode, country) VALUES ('IQ', 'IRAQ');
INSERT INTO Countries (ccode, country) VALUES ('IR', 'IRAN, ISLAMIC REPUBLIC OF');
INSERT INTO Countries (ccode, country) VALUES ('IS', 'ICELAND');
INSERT INTO Countries (ccode, country) VALUES ('IT', 'ITALY');
INSERT INTO Countries (ccode, country) VALUES ('JM', 'JAMAICA');
INSERT INTO Countries (ccode, country) VALUES ('JO', 'JORDAN');
INSERT INTO Countries (ccode, country) VALUES ('JP', 'JAPAN');
INSERT INTO Countries (ccode, country) VALUES ('KE', 'KENYA');
INSERT INTO Countries (ccode, country) VALUES ('KG', 'KYRGYZSTAN');
INSERT INTO Countries (ccode, country) VALUES ('KH', 'CAMBODIA');
INSERT INTO Countries (ccode, country) VALUES ('KI', 'KIRIBATI');
INSERT INTO Countries (ccode, country) VALUES ('KM', 'COMOROS');
INSERT INTO Countries (ccode, country) VALUES ('KN', 'SAINT KITTS AND NEVIS');
INSERT INTO Countries (ccode, country) VALUES ('KP', 'KOREA, DEMOCRATIC PEOPLE''S REPUBLIC OF');
INSERT INTO Countries (ccode, country) VALUES ('KR', 'KOREA, REPUBLIC OF');
INSERT INTO Countries (ccode, country) VALUES ('KW', 'KUWAIT');
INSERT INTO Countries (ccode, country) VALUES ('KY', 'CAYMAN ISLANDS');
INSERT INTO Countries (ccode, country) VALUES ('KZ', 'KAZAKHSTAN');
INSERT INTO Countries (ccode, country) VALUES ('LA', 'LAO PEOPLE''S DEMOCRATIC REPUBLIC');
INSERT INTO Countries (ccode, country) VALUES ('LB', 'LEBANON');
INSERT INTO Countries (ccode, country) VALUES ('LC', 'SAINT LUCIA');
INSERT INTO Countries (ccode, country) VALUES ('LI', 'LIECHTENSTEIN');
INSERT INTO Countries (ccode, country) VALUES ('LK', 'SRI LANKA');
INSERT INTO Countries (ccode, country) VALUES ('LR', 'LIBERIA');
INSERT INTO Countries (ccode, country) VALUES ('LS', 'LESOTHO');
INSERT INTO Countries (ccode, country) VALUES ('LT', 'LITHUANIA');
INSERT INTO Countries (ccode, country) VALUES ('LU', 'LUXEMBOURG');
INSERT INTO Countries (ccode, country) VALUES ('LV', 'LATVIA');
INSERT INTO Countries (ccode, country) VALUES ('LY', 'LIBYAN ARAB JAMAHIRIYA');
INSERT INTO Countries (ccode, country) VALUES ('MA', 'MOROCCO');
INSERT INTO Countries (ccode, country) VALUES ('MC', 'MONACO');
INSERT INTO Countries (ccode, country) VALUES ('MD', 'MOLDOVA, REPUBLIC OF');
INSERT INTO Countries (ccode, country) VALUES ('MG', 'MADAGASCAR');
INSERT INTO Countries (ccode, country) VALUES ('MH', 'MARSHALL ISLANDS');
INSERT INTO Countries (ccode, country) VALUES ('MK', 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF');
INSERT INTO Countries (ccode, country) VALUES ('ML', 'MALI');
INSERT INTO Countries (ccode, country) VALUES ('MM', 'MYANMAR');
INSERT INTO Countries (ccode, country) VALUES ('MN', 'MONGOLIA');
INSERT INTO Countries (ccode, country) VALUES ('MO', 'MACAO');
INSERT INTO Countries (ccode, country) VALUES ('MP', 'NORTHERN MARIANA ISLANDS');
INSERT INTO Countries (ccode, country) VALUES ('MQ', 'MARTINIQUE');
INSERT INTO Countries (ccode, country) VALUES ('MR', 'MAURITANIA');
INSERT INTO Countries (ccode, country) VALUES ('MS', 'MONTSERRAT');
INSERT INTO Countries (ccode, country) VALUES ('MT', 'MALTA');
INSERT INTO Countries (ccode, country) VALUES ('MU', 'MAURITIUS');
INSERT INTO Countries (ccode, country) VALUES ('MV', 'MALDIVES');
INSERT INTO Countries (ccode, country) VALUES ('MW', 'MALAWI');
INSERT INTO Countries (ccode, country) VALUES ('MX', 'MEXICO');
INSERT INTO Countries (ccode, country) VALUES ('MY', 'MALAYSIA');
INSERT INTO Countries (ccode, country) VALUES ('MZ', 'MOZAMBIQUE');
INSERT INTO Countries (ccode, country) VALUES ('NA', 'NAMIBIA');
INSERT INTO Countries (ccode, country) VALUES ('NC', 'NEW CALEDONIA');
INSERT INTO Countries (ccode, country) VALUES ('NE', 'NIGER');
INSERT INTO Countries (ccode, country) VALUES ('NF', 'NORFOLK ISLAND');
INSERT INTO Countries (ccode, country) VALUES ('NG', 'NIGERIA');
INSERT INTO Countries (ccode, country) VALUES ('NI', 'NICARAGUA');
INSERT INTO Countries (ccode, country) VALUES ('NL', 'NETHERLANDS');
INSERT INTO Countries (ccode, country) VALUES ('NO', 'NORWAY');
INSERT INTO Countries (ccode, country) VALUES ('NP', 'NEPAL');
INSERT INTO Countries (ccode, country) VALUES ('NR', 'NAURU');
INSERT INTO Countries (ccode, country) VALUES ('NU', 'NIUE');
INSERT INTO Countries (ccode, country) VALUES ('NZ', 'NEW ZEALAND');
INSERT INTO Countries (ccode, country) VALUES ('OM', 'OMAN');
INSERT INTO Countries (ccode, country) VALUES ('PA', 'PANAMA');
INSERT INTO Countries (ccode, country) VALUES ('PE', 'PERU');
INSERT INTO Countries (ccode, country) VALUES ('PF', 'FRENCH POLYNESIA');
INSERT INTO Countries (ccode, country) VALUES ('PG', 'PAPUA NEW GUINEA');
INSERT INTO Countries (ccode, country) VALUES ('PH', 'PHILIPPINES');
INSERT INTO Countries (ccode, country) VALUES ('PK', 'PAKISTAN');
INSERT INTO Countries (ccode, country) VALUES ('PL', 'POLAND');
INSERT INTO Countries (ccode, country) VALUES ('PM', 'SAINT PIERRE AND MIQUELON');
INSERT INTO Countries (ccode, country) VALUES ('PN', 'PITCAIRN');
INSERT INTO Countries (ccode, country) VALUES ('PR', 'PUERTO RICO');
INSERT INTO Countries (ccode, country) VALUES ('PS', 'PALESTINIAN TERRITORY, OCCUPIED');
INSERT INTO Countries (ccode, country) VALUES ('PT', 'PORTUGAL');
INSERT INTO Countries (ccode, country) VALUES ('PW', 'PALAU');
INSERT INTO Countries (ccode, country) VALUES ('PY', 'PARAGUAY');
INSERT INTO Countries (ccode, country) VALUES ('QA', 'QATAR');
INSERT INTO Countries (ccode, country) VALUES ('RE', 'RÉUNION');
INSERT INTO Countries (ccode, country) VALUES ('RO', 'ROMANIA');
INSERT INTO Countries (ccode, country) VALUES ('RU', 'RUSSIAN FEDERATION');
INSERT INTO Countries (ccode, country) VALUES ('RW', 'RWANDA');
INSERT INTO Countries (ccode, country) VALUES ('SA', 'SAUDI ARABIA');
INSERT INTO Countries (ccode, country) VALUES ('SB', 'SOLOMON ISLANDS');
INSERT INTO Countries (ccode, country) VALUES ('SC', 'SEYCHELLES');
INSERT INTO Countries (ccode, country) VALUES ('SD', 'SUDAN');
INSERT INTO Countries (ccode, country) VALUES ('SE', 'SWEDEN');
INSERT INTO Countries (ccode, country) VALUES ('SG', 'SINGAPORE');
INSERT INTO Countries (ccode, country) VALUES ('SH', 'SAINT HELENA');
INSERT INTO Countries (ccode, country) VALUES ('SI', 'SLOVENIA');
INSERT INTO Countries (ccode, country) VALUES ('SJ', 'SVALBARD AND JAN MAYEN');
INSERT INTO Countries (ccode, country) VALUES ('SK', 'SLOVAKIA');
INSERT INTO Countries (ccode, country) VALUES ('SL', 'SIERRA LEONE');
INSERT INTO Countries (ccode, country) VALUES ('SM', 'SAN MARINO');
INSERT INTO Countries (ccode, country) VALUES ('SN', 'SENEGAL');
INSERT INTO Countries (ccode, country) VALUES ('SO', 'SOMALIA');
INSERT INTO Countries (ccode, country) VALUES ('SR', 'SURINAME');
INSERT INTO Countries (ccode, country) VALUES ('ST', 'SAO TOME AND PRINCIPE');
INSERT INTO Countries (ccode, country) VALUES ('SV', 'EL SALVADOR');
INSERT INTO Countries (ccode, country) VALUES ('SY', 'SYRIAN ARAB REPUBLIC');
INSERT INTO Countries (ccode, country) VALUES ('SZ', 'SWAZILAND');
INSERT INTO Countries (ccode, country) VALUES ('TC', 'TURKS AND CAICOS ISLANDS');
INSERT INTO Countries (ccode, country) VALUES ('TD', 'CHAD');
INSERT INTO Countries (ccode, country) VALUES ('TF', 'FRENCH SOUTHERN TERRITORIES');
INSERT INTO Countries (ccode, country) VALUES ('TG', 'TOGO');
INSERT INTO Countries (ccode, country) VALUES ('TH', 'THAILAND');
INSERT INTO Countries (ccode, country) VALUES ('TJ', 'TAJIKISTAN');
INSERT INTO Countries (ccode, country) VALUES ('TK', 'TOKELAU');
INSERT INTO Countries (ccode, country) VALUES ('TL', 'TIMOR-LESTE');
INSERT INTO Countries (ccode, country) VALUES ('TM', 'TURKMENISTAN');
INSERT INTO Countries (ccode, country) VALUES ('TN', 'TUNISIA');
INSERT INTO Countries (ccode, country) VALUES ('TO', 'TONGA');
INSERT INTO Countries (ccode, country) VALUES ('TR', 'TURKEY');
INSERT INTO Countries (ccode, country) VALUES ('TT', 'TRINIDAD AND TOBAGO');
INSERT INTO Countries (ccode, country) VALUES ('TV', 'TUVALU');
INSERT INTO Countries (ccode, country) VALUES ('TW', 'TAIWAN, PROVINCE OF CHINA');
INSERT INTO Countries (ccode, country) VALUES ('TZ', 'TANZANIA, UNITED REPUBLIC OF');
INSERT INTO Countries (ccode, country) VALUES ('UA', 'UKRAINE');
INSERT INTO Countries (ccode, country) VALUES ('UG', 'UGANDA');
INSERT INTO Countries (ccode, country) VALUES ('UM', 'UNITED STATES MINOR OUTLYING ISLANDS');
INSERT INTO Countries (ccode, country) VALUES ('US', 'UNITED STATES');
INSERT INTO Countries (ccode, country) VALUES ('UY', 'URUGUAY');
INSERT INTO Countries (ccode, country) VALUES ('UZ', 'UZBEKISTAN');
INSERT INTO Countries (ccode, country) VALUES ('VA', 'HOLY (VATICAN CITY STATE)');
INSERT INTO Countries (ccode, country) VALUES ('VC', 'SAINT VINCENT AND THE GRENADINES');
INSERT INTO Countries (ccode, country) VALUES ('VE', 'VENEZUELA');
INSERT INTO Countries (ccode, country) VALUES ('VG', 'VIRGIN ISLANDS, BRITISH');
INSERT INTO Countries (ccode, country) VALUES ('VI', 'VIRGIN ISLANDS, U.S.');
INSERT INTO Countries (ccode, country) VALUES ('VN', 'VIET NAM');
INSERT INTO Countries (ccode, country) VALUES ('VU', 'VANUATU');
INSERT INTO Countries (ccode, country) VALUES ('WF', 'WALLIS AND FUTUNA');
INSERT INTO Countries (ccode, country) VALUES ('WS', 'SAMOA');
INSERT INTO Countries (ccode, country) VALUES ('YE', 'YEMEN');
INSERT INTO Countries (ccode, country) VALUES ('YT', 'MAYOTTE');
INSERT INTO Countries (ccode, country) VALUES ('ZA', 'SOUTH AFRICA');
INSERT INTO Countries (ccode, country) VALUES ('ZM', 'ZAMBIA');
INSERT INTO Countries (ccode, country) VALUES ('ZW', 'ZIMBABWE');

------------------------------------------------------------
-- Populate Source table.                                 --
------------------------------------------------------------

INSERT INTO Source (text) VALUES ('About');
INSERT INTO Source (text) VALUES ('Add');
INSERT INTO Source (text) VALUES ('Advertised Sales Price');
INSERT INTO Source (text) VALUES ('Amount');
INSERT INTO Source (text) VALUES ('Average Purchasing Price');
INSERT INTO Source (text) VALUES ('Average Selling Price');
INSERT INTO Source (text) VALUES ('Brand');
INSERT INTO Source (text) VALUES ('Brand Used');
INSERT INTO Source (text) VALUES ('Cancel');
INSERT INTO Source (text) VALUES ('Categories');
INSERT INTO Source (text) VALUES ('Category');
INSERT INTO Source (text) VALUES ('Category Description');
INSERT INTO Source (text) VALUES ('Category ID');
INSERT INTO Source (text) VALUES ('Category Tax');
INSERT INTO Source (text) VALUES ('City');
INSERT INTO Source (text) VALUES ('Close');
INSERT INTO Source (text) VALUES ('Connect');
INSERT INTO Source (text) VALUES ('Container Item');
INSERT INTO Source (text) VALUES ('Container Items');
INSERT INTO Source (text) VALUES ('Container Number');
INSERT INTO Source (text) VALUES ('Containers');
INSERT INTO Source (text) VALUES ('Containers Form');
INSERT INTO Source (text) VALUES ('Could Not Connect');
INSERT INTO Source (text) VALUES ('Could not connect with specified form values.');
INSERT INTO Source (text) VALUES ('Could not find any database drivers.');
INSERT INTO Source (text) VALUES ('Could not insert record because...');
INSERT INTO Source (text) VALUES ('Could not remove record because...');
INSERT INTO Source (text) VALUES ('Could not update record because...');
INSERT INTO Source (text) VALUES ('Customer');
INSERT INTO Source (text) VALUES ('Customer Name');
INSERT INTO Source (text) VALUES ('Customer Price');
INSERT INTO Source (text) VALUES ('Customers');
INSERT INTO Source (text) VALUES ('Customers Form');
INSERT INTO Source (text) VALUES ('Customer Shipments Form');
INSERT INTO Source (text) VALUES ('Damaged');
INSERT INTO Source (text) VALUES ('Database');
INSERT INTO Source (text) VALUES ('Database Connection');
INSERT INTO Source (text) VALUES ('Database Driver');
INSERT INTO Source (text) VALUES ('Database Host');
INSERT INTO Source (text) VALUES ('Database Name');
INSERT INTO Source (text) VALUES ('Database Password');
INSERT INTO Source (text) VALUES ('Database Port');
INSERT INTO Source (text) VALUES ('Database User');
INSERT INTO Source (text) VALUES ('Date');
INSERT INTO Source (text) VALUES ('Date Purchaseed');
INSERT INTO Source (text) VALUES ('Date Received');
INSERT INTO Source (text) VALUES ('Date Sent');
INSERT INTO Source (text) VALUES ('Date Sold');
INSERT INTO Source (text) VALUES ('Description');
INSERT INTO Source (text) VALUES ('Discounted Price');
INSERT INTO Source (text) VALUES ('Done');
INSERT INTO Source (text) VALUES ('E-mail Address');
INSERT INTO Source (text) VALUES ('Error encountered while executing query.');
INSERT INTO Source (text) VALUES ('Exit');
INSERT INTO Source (text) VALUES ('Fax Number');
INSERT INTO Source (text) VALUES ('First Name');
INSERT INTO Source (text) VALUES ('Forward');
INSERT INTO Source (text) VALUES ('Help');
INSERT INTO Source (text) VALUES ('History');
INSERT INTO Source (text) VALUES ('History Form');
INSERT INTO Source (text) VALUES ('Home Phone Number');
INSERT INTO Source (text) VALUES ('ID');
INSERT INTO Source (text) VALUES ('Incoming');
INSERT INTO Source (text) VALUES ('In Stock');
INSERT INTO Source (text) VALUES ('Inventory Form');
INSERT INTO Source (text) VALUES ('Item');
INSERT INTO Source (text) VALUES ('Item Brand');
INSERT INTO Source (text) VALUES ('Item Catalogue');
INSERT INTO Source (text) VALUES ('Item Code');
INSERT INTO Source (text) VALUES ('Item Description');
INSERT INTO Source (text) VALUES ('Item Received from Customer');
INSERT INTO Source (text) VALUES ('Items');
INSERT INTO Source (text) VALUES ('Item Sent to Customer');
INSERT INTO Source (text) VALUES ('Items Received from Customers');
INSERT INTO Source (text) VALUES ('Items Sent to Customers');
INSERT INTO Source (text) VALUES ('Item Used');
INSERT INTO Source (text) VALUES ('Last Name');
INSERT INTO Source (text) VALUES ('Location');
INSERT INTO Source (text) VALUES ('Loose');
INSERT INTO Source (text) VALUES ('Manual');
INSERT INTO Source (text) VALUES ('Modify');
INSERT INTO Source (text) VALUES ('Name');
INSERT INTO Source (text) VALUES ('No Database Drivers Found');
INSERT INTO Source (text) VALUES ('none');
INSERT INTO Source (text) VALUES ('Number Sold');
INSERT INTO Source (text) VALUES ('Operation Error');
INSERT INTO Source (text) VALUES ('Operation Not Permitted');
INSERT INTO Source (text) VALUES ('Options');
INSERT INTO Source (text) VALUES ('Order ID');
INSERT INTO Source (text) VALUES ('Other');
INSERT INTO Source (text) VALUES ('Outgoing');
INSERT INTO Source (text) VALUES ('Pending');
INSERT INTO Source (text) VALUES ('Pending Purchase Item');
INSERT INTO Source (text) VALUES ('Pending Purchase Items');
INSERT INTO Source (text) VALUES ('Pending Purchase Items Form');
INSERT INTO Source (text) VALUES ('Pending Sale Item');
INSERT INTO Source (text) VALUES ('Pending Sale Items');
INSERT INTO Source (text) VALUES ('Phone Number');
INSERT INTO Source (text) VALUES ('Postal Code');
INSERT INTO Source (text) VALUES ('Purchase');
INSERT INTO Source (text) VALUES ('Purchased');
INSERT INTO Source (text) VALUES ('Purchase Date');
INSERT INTO Source (text) VALUES ('Purchase Description');
INSERT INTO Source (text) VALUES ('Purchased Items');
INSERT INTO Source (text) VALUES ('Purchase ID');
INSERT INTO Source (text) VALUES ('Purchase Item');
INSERT INTO Source (text) VALUES ('Purchase Items');
INSERT INTO Source (text) VALUES ('Purchase Number');
INSERT INTO Source (text) VALUES ('Purchases');
INSERT INTO Source (text) VALUES ('Purchases Form');
INSERT INTO Source (text) VALUES ('Quantity');
INSERT INTO Source (text) VALUES ('Quantity Pending');
INSERT INTO Source (text) VALUES ('Quantity Received');
INSERT INTO Source (text) VALUES ('Quantity Sent');
INSERT INTO Source (text) VALUES ('Quantity Sold');
INSERT INTO Source (text) VALUES ('Quantity Stored');
INSERT INTO Source (text) VALUES ('Query Error');
INSERT INTO Source (text) VALUES ('Really Exit');
INSERT INTO Source (text) VALUES ('Really Exit Application');
INSERT INTO Source (text) VALUES ('Received');
INSERT INTO Source (text) VALUES ('Received Supplier Item');
INSERT INTO Source (text) VALUES ('Received Supplier Items');
INSERT INTO Source (text) VALUES ('Record could not be added because...');
INSERT INTO Source (text) VALUES ('Record could not be deleted because...');
INSERT INTO Source (text) VALUES ('Record could not be modified because...');
INSERT INTO Source (text) VALUES ('Remarks');
INSERT INTO Source (text) VALUES ('Remember Password');
INSERT INTO Source (text) VALUES ('Remove');
INSERT INTO Source (text) VALUES ('Representative');
INSERT INTO Source (text) VALUES ('Representative ID');
INSERT INTO Source (text) VALUES ('Representative Location');
INSERT INTO Source (text) VALUES ('Representative Name');
INSERT INTO Source (text) VALUES ('Representatives');
INSERT INTO Source (text) VALUES ('Representatives Form');
INSERT INTO Source (text) VALUES ('Requested Brand');
INSERT INTO Source (text) VALUES ('Requested Item');
INSERT INTO Source (text) VALUES ('Requested Supplier');
INSERT INTO Source (text) VALUES ('Sale');
INSERT INTO Source (text) VALUES ('Sale Date');
INSERT INTO Source (text) VALUES ('Sale Description');
INSERT INTO Source (text) VALUES ('Sale ID');
INSERT INTO Source (text) VALUES ('Sale Item');
INSERT INTO Source (text) VALUES ('Sale Items');
INSERT INTO Source (text) VALUES ('Sale Number');
INSERT INTO Source (text) VALUES ('Sales');
INSERT INTO Source (text) VALUES ('Sales Form');
INSERT INTO Source (text) VALUES ('Sales Location');
INSERT INTO Source (text) VALUES ('Sales Representative');
INSERT INTO Source (text) VALUES ('Satisfied');
INSERT INTO Source (text) VALUES ('Send Sold Items Form');
INSERT INTO Source (text) VALUES ('Sent');
INSERT INTO Source (text) VALUES ('Sent Supplier Item');
INSERT INTO Source (text) VALUES ('Sent Supplier Items');
INSERT INTO Source (text) VALUES ('Snapshots');
INSERT INTO Source (text) VALUES ('Sold');
INSERT INTO Source (text) VALUES ('Sold Items');
INSERT INTO Source (text) VALUES ('standard');
INSERT INTO Source (text) VALUES ('Stored');
INSERT INTO Source (text) VALUES ('Street');
INSERT INTO Source (text) VALUES ('Submit');
INSERT INTO Source (text) VALUES ('Substitute Items');
INSERT INTO Source (text) VALUES ('Supplier');
INSERT INTO Source (text) VALUES ('Supplier Brand Items Form');
INSERT INTO Source (text) VALUES ('Supplier Brands');
INSERT INTO Source (text) VALUES ('Supplier Brands Form');
INSERT INTO Source (text) VALUES ('Supplier Name');
INSERT INTO Source (text) VALUES ('Suppliers');
INSERT INTO Source (text) VALUES ('Suppliers Form');
INSERT INTO Source (text) VALUES ('Supplier Shipments Form');
INSERT INTO Source (text) VALUES ('Supplier Used');
INSERT INTO Source (text) VALUES ('Take Inventory Snapshot');
INSERT INTO Source (text) VALUES ('Take Inventory Snapshot before quitting?');
INSERT INTO Source (text) VALUES ('Take Snapshot');
INSERT INTO Source (text) VALUES ('Tax');
INSERT INTO Source (text) VALUES ('Tax Categories');
INSERT INTO Source (text) VALUES ('Tax Categories Form');
INSERT INTO Source (text) VALUES ('Tax Category');
INSERT INTO Source (text) VALUES ('Tax Percentage');
INSERT INTO Source (text) VALUES ('Unit Price');
INSERT INTO Source (text) VALUES ('Version');
INSERT INTO Source (text) VALUES ('Work Phone Number');

------------------------------------------------------------
-- Populate Mentions table.                               --
------------------------------------------------------------

INSERT INTO Mentions (mentionid, mention) VALUES (1, 'Please mention my name and e-mail address on the project website and in the CONTRIBUTORS file.');
INSERT INTO Mentions (mentionid, mention) VALUES (2, 'Please mention my name on the project website and my name and e-mail address in the CONTRIBUTORS file.');
INSERT INTO Mentions (mentionid, mention) VALUES (3, 'Please mention my name on the project website and in the CONTRIBUTORS file.');
INSERT INTO Mentions (mentionid, mention) VALUES (4, 'Please mention my name and e-mail address in the CONTRIBUTORS file.');
INSERT INTO Mentions (mentionid, mention) VALUES (5, 'Please mention my name in the CONTRIBUTORS file.');
INSERT INTO Mentions (mentionid, mention) VALUES (6, 'Please do not mention my name or e-mail address anywhere.');
