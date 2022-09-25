CREATE TABLE ev.referral(ACCESSION_NUMBER VARCHAR(100) NOT NULL,RECEIVER_ID VARCHAR(50),RECEIVER_ID_TYPE VARCHAR(50) NOT NULL,CCRECEIVER_ID VARCHAR(50),CCRECEIVER_ID_TYPE VARCHAR(50) NOT NULL,PRIMARY KEY (ACCESSION_NUMBER)) IN DATABASE testdb;

INSERT INTO ev.referral VALUES ('RN.54', '80010911', 'sygehusafdelingsnummer', '123456', 'ydernummer');
INSERT INTO ev.referral VALUES ('RN.42', '80010811', 'sygehusafdelingsnummer', '654321', 'ydernummer');
INSERT INTO ev.referral VALUES ('RN.53', '80010711', 'sygehusafdelingsnummer', '12354654131456', 'sorkode');
INSERT INTO ev.referral VALUES ('RN.59', '80010711', 'sygehusafdelingsnummer', '12354654131456', 'sorkode');
INSERT INTO ev.referral VALUES ('RN.51', '457985456461', 'sorkode', '55555577777777', 'lokationsnummer');