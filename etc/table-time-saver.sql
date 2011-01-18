CREATE TABLE `fligtar_panorama`.`contributions_annoyance` (
`date` DATE NOT NULL DEFAULT '0000-00-00',
`annoyance1_amt_earned` DECIMAL(6,2) UNSIGNED NOT NULL DEFAULT '0.00',
`annoyance1_amt_avg` DECIMAL(6,2) UNSIGNED NOT NULL DEFAULT '0.00',
`annoyance1_amt_min` DECIMAL(6,2) UNSIGNED NOT NULL DEFAULT '0.00',
`annoyance1_amt_max` DECIMAL(6,2) UNSIGNED NOT NULL DEFAULT '0.00',
`annoyance1_amt_eq_suggested` INT UNSIGNED NOT NULL DEFAULT '0',
`annoyance1_amt_gt_suggested` INT UNSIGNED NOT NULL DEFAULT '0',
`annoyance1_amt_lt_suggested` INT UNSIGNED NOT NULL DEFAULT '0',
`annoyance1_tx_success` INT UNSIGNED NOT NULL DEFAULT '0',
`annoyance1_tx_abort` INT UNSIGNED NOT NULL DEFAULT '0',

`annoyance2_amt_earned` DECIMAL(6,2) UNSIGNED NOT NULL DEFAULT '0.00',
`annoyance2_amt_avg` DECIMAL(6,2) UNSIGNED NOT NULL DEFAULT '0.00',
`annoyance2_amt_min` DECIMAL(6,2) UNSIGNED NOT NULL DEFAULT '0.00',
`annoyance2_amt_max` DECIMAL(6,2) UNSIGNED NOT NULL DEFAULT '0.00',
`annoyance2_amt_eq_suggested` INT UNSIGNED NOT NULL DEFAULT '0',
`annoyance2_amt_gt_suggested` INT UNSIGNED NOT NULL DEFAULT '0',
`annoyance2_amt_lt_suggested` INT UNSIGNED NOT NULL DEFAULT '0',
`annoyance2_tx_success` INT UNSIGNED NOT NULL DEFAULT '0',
`annoyance2_tx_abort` INT UNSIGNED NOT NULL DEFAULT '0',

`annoyance3_amt_earned` DECIMAL(6,2) UNSIGNED NOT NULL DEFAULT '0.00',
`annoyance3_amt_avg` DECIMAL(6,2) UNSIGNED NOT NULL DEFAULT '0.00',
`annoyance3_amt_min` DECIMAL(6,2) UNSIGNED NOT NULL DEFAULT '0.00',
`annoyance3_amt_max` DECIMAL(6,2) UNSIGNED NOT NULL DEFAULT '0.00',
`annoyance3_amt_eq_suggested` INT UNSIGNED NOT NULL DEFAULT '0',
`annoyance3_amt_gt_suggested` INT UNSIGNED NOT NULL DEFAULT '0',
`annoyance3_amt_lt_suggested` INT UNSIGNED NOT NULL DEFAULT '0',
`annoyance3_tx_success` INT UNSIGNED NOT NULL DEFAULT '0',
`annoyance3_tx_abort` INT UNSIGNED NOT NULL DEFAULT '0',

PRIMARY KEY ( `date` )
) ENGINE = InnoDB;