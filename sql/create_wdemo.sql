CREATE TABLE ev.referral(ACCESSION_NUMBER VARCHAR(100) NOT NULL,SENDER_ID VARCHAR(50),SENDER_ID_TYPE VARCHAR(50) NOT NULL,CCRECEIVER_ID VARCHAR(50),CCRECEIVER_ID_TYPE VARCHAR(50) NOT NULL,PRIMARY KEY (ACCESSION_NUMBER)) IN DATABASE testdb;

INSERT INTO ev.referral VALUES ('RN.54', '80010911', 'sygehusafdelingsnummer', '123456', 'ydernummer');
INSERT INTO ev.referral VALUES ('RN.42', '80010811', 'sygehusafdelingsnummer', '654321', 'ydernummer');
INSERT INTO ev.referral VALUES ('RN.53', '80010711', 'sygehusafdelingsnummer', '12354654131456', 'sorkode');
INSERT INTO ev.referral VALUES ('RN.59', '80010711', 'sygehusafdelingsnummer', '12354654131456', 'sorkode');
INSERT INTO ev.referral VALUES ('RN.51', '457985456461', 'sorkode', '55555577777777', 'lokationsnummer');
INSERT INTO ev.referral VALUES ('RN.29234', '80010811', 'sygehusafdelingsnummer', '654321', 'ydernummer');
INSERT INTO ev.referral VALUES ('RN.67', '80010811', 'sygehusafdelingsnummer', '000000', 'ydernummer');
INSERT INTO ev.referral VALUES ('RN.311', '80010811', 'sygehusafdelingsnummer', '654321', 'ydernummer');
INSERT INTO ev.referral VALUES ('RN.2493', '000000', 'ydernummer', '654321', 'ydernummer');
INSERT INTO ev.referral VALUES ('RN.1310J', '000000', 'ydernummer', '654321', 'ydernummer');
INSERT INTO ev.referral VALUES ('RN.302938', '000000', 'ydernummer', '', '');
INSERT INTO ev.referral VALUES ('RN.1100002', '80010811', 'ydernummer', '', '');