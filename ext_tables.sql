
#
# Table structure for table 'tx_blockmaliciousloginattempts_failed_login'
#
CREATE TABLE tx_blockmaliciousloginattempts_failed_login (
    uid int(11) NOT NULL auto_increment,
    ip varchar(255) DEFAULT '' NOT NULL,
    username varchar(255) DEFAULT '' NOT NULL,
    time int(11) DEFAULT '0' NOT NULL,

    PRIMARY KEY (uid),
);
