<?php
/*
 * fpanel — a web-based hosting control panel.
 * Copyright (C) 2026  Ahmet Refik Gunes
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */namespace FPanel\System;

use Phalcon\Mvc\User\Component;

class Database extends Component
{
    public function getBackup(): string
    {
        return "-- ----------------------------
-- Table structure for backup
-- ----------------------------
DROP TABLE IF EXISTS `backup`;
CREATE TABLE `backup`  (
  `id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `domain_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `client_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `server_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `repository` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'server',
  `ftp_repository_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `storage_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `suspend` tinyint(1) UNSIGNED NULL DEFAULT 0,
  `created_date` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `progress` int(11) UNSIGNED NULL DEFAULT NULL,
  `description` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `content` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `sizes` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `restore_contents` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `progress_data` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `backup_id_idx`(`id`) USING BTREE,
  INDEX `backup_domain_id_idx`(`domain_id`) USING BTREE,
  INDEX `backup_client_id_idx`(`client_id`) USING BTREE,
  INDEX `backup_server_id_idx`(`server_id`) USING BTREE,
  INDEX `backup_name_idx`(`name`) USING BTREE,
  INDEX `backup_type_idx`(`type`) USING BTREE,
  INDEX `backup_ftp_repository_id_fk`(`ftp_repository_id`) USING BTREE,
  INDEX `backup_storage_id_fk`(`storage_id`) USING BTREE,
  CONSTRAINT `backup_domain_id_fk` FOREIGN KEY (`domain_id`) REFERENCES `domains` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `backup_client_id_fk` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `backup_server_id_fk` FOREIGN KEY (`server_id`) REFERENCES `servers` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `backup_ftp_repository_id_fk` FOREIGN KEY (`ftp_repository_id`) REFERENCES `ftp_repo` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `backup_storage_id_fk` FOREIGN KEY (`storage_id`) REFERENCES `cloud_storage` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

        ";
    }

    public function getBlacklist(): string
    {
        return "
-- ----------------------------
-- Table structure for blacklist
-- ----------------------------
DROP TABLE IF EXISTS `blacklist`;
CREATE TABLE `blacklist`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `rule` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `sb_id_idx`(`id`) USING BTREE,
  INDEX `sb_rule_idx`(`rule`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getCertificates(): string
    {
        return "-- ----------------------------
-- Table structure for certificates
-- ----------------------------
DROP TABLE IF EXISTS `certificates`;
CREATE TABLE `certificates`  (
  `id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `domain_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `csr` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `private_key` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `crt` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `ca` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `bit` int UNSIGNED NULL DEFAULT 1,
  `country` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `state` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `city` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `company` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `department` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `domain_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `common_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `valid_to` datetime(0) NULL DEFAULT NULL,
  `valid_from` datetime(0) NULL DEFAULT NULL,
  `signature_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `certificates_id_idx`(`id`) USING BTREE,
  UNIQUE INDEX `certificates_name_idx`(`name`) USING BTREE,
  INDEX `certificates_domain_id_idx`(`domain_id`) USING BTREE,
  INDEX `certificates_domain_name_idx`(`domain_name`) USING BTREE,
  CONSTRAINT `certificates_domain_id_fk` FOREIGN KEY (`domain_id`) REFERENCES `domains` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getClientIpAddress(): string
    {
        return "-- ----------------------------
-- Table structure for client_ip_address
-- ----------------------------
DROP TABLE IF EXISTS `client_ip_address`;
CREATE TABLE `client_ip_address`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ipaddr_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `client_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `client_ip_address_id_idx`(`id`) USING BTREE,
  INDEX `client_ip_address_ipaddr_id_idx`(`ipaddr_id`) USING BTREE,
  INDEX `client_ip_address_client_id_idx`(`client_id`) USING BTREE,
  CONSTRAINT `client_ip_addr_client_id_fk` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `client_ip_addr_ipaddr_id_fk` FOREIGN KEY (`ipaddr_id`) REFERENCES `ip_address` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 500000011 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getClientLimitsPermissions(): string
    {
        return "-- ----------------------------
-- Table structure for client_limits_permissions
-- ----------------------------
DROP TABLE IF EXISTS `client_limits_permissions`;
CREATE TABLE `client_limits_permissions`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `domain_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `client_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `client_limit` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'unlimited',
  `domain_limit` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'unlimited',
  `disk_limit` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'unlimited',
  `traffic_limit` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'unlimited',
  `subdomain_limit` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'unlimited',
  `domain_alias_limit` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'unlimited',
  `email_box_limit` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'unlimited',
  `email_box_size_limit` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'unlimited',
  `email_forward_limit` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'unlimited',
  `email_groups_limit` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'unlimited',
  `email_autoresponder_limit` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'unlimited',
  `email_lists_limit` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'unlimited',
  `database_limit` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'unlimited',
  `database_size_limit` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'unlimited',
  `template_limit` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'unlimited',
  `access_panel` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `man_client` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `man_domain` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `man_subdomain` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `man_domain_alias` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `man_email_alias` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `man_hosting_settings` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `man_task` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `man_dns` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `man_mailing_lists` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `man_spam_filters` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `man_backup` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `man_email` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `man_database` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `man_protect_dir` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `man_ssl` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `man_template` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `overuse` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `php` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `php_version` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `cgi` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `perl` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `python` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `fast_cgi` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `statistic_support` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `statistic_type` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `asp` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `aspnet` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `aspnet_core` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `aspnet_version` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `dedicated_pool` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `max_cpu` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `application_pool_mode` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `ssl` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `clp_client_id_index`(`client_id`) USING BTREE,
  UNIQUE INDEX `clp_id_idx`(`id`) USING BTREE,
  UNIQUE INDEX `clp_domain_id_index`(`domain_id`) USING BTREE,
  CONSTRAINT `clp_domain_id_fk` FOREIGN KEY (`domain_id`) REFERENCES `domains` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `clp_client_id_fk` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getClientStatistics(): string
    {
        return "-- ----------------------------
-- Table structure for client_statistics
-- ----------------------------
DROP TABLE IF EXISTS `client_statistics`;
CREATE TABLE `client_statistics`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `account_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `client_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `parent_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `mail` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `mail_max` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `db` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `db_max` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `httpdoc` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `disk` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `disk_max` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `traffic` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `traffic_max` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `logs` bigint(20) UNSIGNED NOT NULL,
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `statistic` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `over_use` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `expire_date` datetime(0) NULL DEFAULT NULL,
  `last_init` datetime(0) NOT NULL,
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `saccount_id_idx`(`id`) USING BTREE,
  INDEX `saccount_account_name_idx`(`account_name`) USING BTREE,
  INDEX `saccount_client_id_idx`(`client_id`) USING BTREE,
  INDEX `saccount_parent_id_idx`(`parent_id`) USING BTREE,
  CONSTRAINT `cls_client_id_fk` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `cls_parent_id_fk` FOREIGN KEY (`parent_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getCloudStorage(): string
    {
        return "-- ----------------------------
-- Table structure for cloud_storage
-- ----------------------------
DROP TABLE IF EXISTS `cloud_storage`;
CREATE TABLE `cloud_storage`  (
  `id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `client_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `params` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `cs_id_idx`(`id`) USING BTREE,
  INDEX `cs_client_id_idx`(`client_id`) USING BTREE,
  INDEX `cs_name_idx`(`name`) USING BTREE,
  CONSTRAINT `cloud_storage_client_id_fk` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getDatabaseRemoteHosts(): string
    {
        return "-- ----------------------------
-- Table structure for database_remote_hosts
-- ----------------------------
DROP TABLE IF EXISTS `database_remote_hosts`;
CREATE TABLE `database_remote_hosts`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `hosts` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `db_user_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `drh_id_idx`(`id`) USING BTREE,
  INDEX `drh_db_user_id_idx`(`db_user_id`) USING BTREE,
  CONSTRAINT `drh_db_user_id_fk` FOREIGN KEY (`db_user_id`) REFERENCES `database_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getDatabaseServer(): string
    {
        return "-- ----------------------------
-- Table structure for database_server
-- ----------------------------
DROP TABLE IF EXISTS `database_server`;
CREATE TABLE `database_server`  (
  `id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `db_server_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `host` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `port` int(10) UNSIGNED NOT NULL,
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `last_error` tinyint(1) UNSIGNED NULL DEFAULT 0,
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `server_version` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL,
  `server_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `sds_id_idx`(`id`) USING BTREE,
  UNIQUE INDEX `sds_db_server_name_idx`(`db_server_name`) USING BTREE,
  INDEX `sds_server_id_idx`(`server_id`) USING BTREE,
  CONSTRAINT `database_server_server_id_fk` FOREIGN KEY (`server_id`) REFERENCES `servers` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getDatabaseServerSettings(): string
    {
        return "-- ----------------------------
-- Table structure for database_server_settings
-- ----------------------------
DROP TABLE IF EXISTS `database_server_settings`;
CREATE TABLE `database_server_settings`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `params` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `database_server_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `dss_id_idx`(`id`) USING BTREE,
  INDEX `dss_database_server_id_idx`(`database_server_id`) USING BTREE,
  CONSTRAINT `dss_database_server_id_fk` FOREIGN KEY (`database_server_id`) REFERENCES `database_server` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getDatabaseUsers(): string
    {
        return "-- ----------------------------
-- Table structure for database_users
-- ----------------------------
DROP TABLE IF EXISTS `database_users`;
CREATE TABLE `database_users`  (
  `id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `login` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `dbu_id`(`id`) USING BTREE,
  UNIQUE INDEX `dbu_login`(`login`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getDatabases(): string
    {
        return "-- ----------------------------
-- Table structure for databases
-- ----------------------------
DROP TABLE IF EXISTS `databases`;
CREATE TABLE `databases`  (
  `id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `domain_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `database_user_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `database_server_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `server_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `db_name_idx`(`name`) USING BTREE,
  INDEX `db_domain_id_idx`(`domain_id`) USING BTREE,
  INDEX `db_type_idx`(`type`) USING BTREE,
  INDEX `db_database_server_id_idx`(`database_server_id`) USING BTREE,
  INDEX `db_database_user_id_idx`(`database_user_id`) USING BTREE,
  INDEX `db_server_id_idx`(`server_id`) USING BTREE,
  CONSTRAINT `databases_domain_id_fk` FOREIGN KEY (`domain_id`) REFERENCES `domains` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `databases_database_user_id_fk` FOREIGN KEY (`database_user_id`) REFERENCES `database_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `databases_database_server_id_fk` FOREIGN KEY (`database_server_id`) REFERENCES `database_server` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `databases_server_id_fk` FOREIGN KEY (`server_id`) REFERENCES `servers` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getDnsRecords(): string
    {
        return "-- ----------------------------
-- Table structure for dns_records
-- ----------------------------
DROP TABLE IF EXISTS `dns_records`;
CREATE TABLE `dns_records`  (
  `id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `dns_zone_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'A',
  `host` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `value` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `opt` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
  `time_stamp` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `order` int(5) UNSIGNED NOT NULL,
  `port` int(5) UNSIGNED NULL DEFAULT NULL,
  `weight` int(5) UNSIGNED NULL DEFAULT NULL,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `dns_type_idx`(`type`) USING BTREE,
  INDEX `dns_zone_id_idx`(`dns_zone_id`) USING BTREE,
  INDEX `dns_order_idx`(`order`) USING BTREE,
  CONSTRAINT `dns_records_dns_zone_id_fk` FOREIGN KEY (`dns_zone_id`) REFERENCES `dns_zone` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getDnsTemplate(): string
    {
        return "-- ----------------------------
-- Table structure for dns_template
-- ----------------------------
DROP TABLE IF EXISTS `dns_template`;
CREATE TABLE `dns_template`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'A',
  `host` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `value` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `opt` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
  `timestamp` int(15) UNSIGNED NULL DEFAULT 0,
  `order` int(10) UNSIGNED NULL DEFAULT NULL,
  `server_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `sdns_id_idx`(`id`) USING BTREE,
  INDEX `sdns_type_idx`(`type`) USING BTREE,
  INDEX `sdns_server_id_idx`(`server_id`) USING BTREE,
  INDEX `sdns_order_idx`(`order`) USING BTREE,
  CONSTRAINT `dns_template_server_id_fk` FOREIGN KEY (`server_id`) REFERENCES `servers` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getDnsZone(): string
    {
        return "-- ----------------------------
-- Table structure for dns_zone
-- ----------------------------
DROP TABLE IF EXISTS `dns_zone`;
CREATE TABLE `dns_zone`  (
  `id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `zone_type` tinyint(1) UNSIGNED NULL DEFAULT NULL,
  `domain_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `domain_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `ttl` int(10) UNSIGNED NOT NULL DEFAULT 86400,
  `refresh` int(10) UNSIGNED NOT NULL DEFAULT 10800,
  `retry` int(10) UNSIGNED NOT NULL DEFAULT 3600,
  `expire` int(10) UNSIGNED NOT NULL DEFAULT 604800,
  `minimum` int(10) UNSIGNED NOT NULL DEFAULT 10800,
  `serial_format` enum('UNIXTIMESTAMP','YYYYMMDDNN') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'UNIXTIMESTAMP',
  `serial` varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `server_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `dnsz_id_idx`(`id`) USING BTREE,
  INDEX `dnsz_domain_id_idx`(`domain_id`) USING BTREE,
  INDEX `dnsz_domain_name_idx`(`domain_name`) USING BTREE,
  INDEX `dnsz_status_idx`(`status`) USING BTREE,
  INDEX `dnsz_server_id_idx`(`server_id`) USING BTREE,
  CONSTRAINT `dns_zone_domain_id_fk` FOREIGN KEY (`domain_id`) REFERENCES `domains` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `dns_zone_server_id_fk` FOREIGN KEY (`server_id`) REFERENCES `servers` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getDomainAlias(): string
    {
        return "-- ----------------------------
-- Table structure for domain_alias
-- ----------------------------
DROP TABLE IF EXISTS `domain_alias`;
CREATE TABLE `domain_alias`  (
  `id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `domain_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `alias_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `syn_pri_dom` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `dns` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `mail` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `web` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `da_alias_name_idx`(`alias_name`) USING BTREE,
  INDEX `da_domain_id_idx`(`domain_id`) USING BTREE,
  CONSTRAINT `domain_alias_domain_id_fk` FOREIGN KEY (`domain_id`) REFERENCES `domains` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getDomainServices(): string
    {
        return "-- ----------------------------
-- Table structure for domain_services
-- ----------------------------
DROP TABLE IF EXISTS `domain_services`;
CREATE TABLE `domain_services`  (
  `domain_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `web` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `dns` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `mail` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`domain_id`) USING BTREE,
  UNIQUE INDEX `ds_domain_id_idx`(`domain_id`) USING BTREE,
  CONSTRAINT `domain_services_domain_id_fk` FOREIGN KEY (`domain_id`) REFERENCES `domains` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getDomainStatistics(): string
    {
        return "-- ----------------------------
-- Table structure for domain_statistics
-- ----------------------------
DROP TABLE IF EXISTS `domain_statistics`;
CREATE TABLE `domain_statistics`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `domain_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `client_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `domain_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `mail` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `mail_max` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `db` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `db_max` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `httpdoc` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `disk` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `disk_max` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `traffic` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `traffic_max` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `logs` bigint(20) UNSIGNED NOT NULL,
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `statistic` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `over_use` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `expire_date` datetime(0) NULL DEFAULT NULL,
  `last_init` datetime(0) NULL DEFAULT NULL,
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `sdomain_domain_id_idx`(`domain_id`) USING BTREE,
  UNIQUE INDEX `sdomain_domain_name_idx`(`domain_name`) USING BTREE,
  INDEX `sdomain_client_id_idx`(`client_id`) USING BTREE,
  CONSTRAINT `domain_statistics_domain_id_fk` FOREIGN KEY (`domain_id`) REFERENCES `domains` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `domain-statistics_client_id_fk` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getDomains(): string
    {
        return "-- ----------------------------
-- Table structure for domains
-- ----------------------------
DROP TABLE IF EXISTS `domains`;
CREATE TABLE `domains`  (
  `id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `client_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ssl_cert_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `ftp_user_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ipaddr_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `status_msg` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `expire_date` datetime(0) NULL DEFAULT NULL,
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  `web_server_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `dns_server_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `mail_server_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `params` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `prefix` varchar(8) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `domains_id_idx`(`id`) USING BTREE,
  UNIQUE INDEX `domains_name_idx`(`name`) USING BTREE,
  INDEX `domains_client_id_idx`(`client_id`) USING BTREE,
  INDEX `domains_web_server_id_idx`(`web_server_id`) USING BTREE,
  INDEX `domains_ipaddr_id_idx`(`ipaddr_id`) USING BTREE,
  INDEX `domains_dns_server_id_idx`(`dns_server_id`) USING BTREE,
  INDEX `domains_mail_server_id_idx`(`mail_server_id`) USING BTREE,
  INDEX `domains_ssl_cert_id_fk`(`ssl_cert_id`) USING BTREE,
  INDEX `domains_ftp_user_id_fk`(`ftp_user_id`) USING BTREE,
  CONSTRAINT `domains_client_id_fk` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `domains_ssl_cert_id_fk` FOREIGN KEY (`ssl_cert_id`) REFERENCES `certificates` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `domains_ftp_user_id_fk` FOREIGN KEY (`ftp_user_id`) REFERENCES `ftp_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `domains_ipaddr_id_fk` FOREIGN KEY (`ipaddr_id`) REFERENCES `ip_address` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `domains_dns_server_id_fk` FOREIGN KEY (`dns_server_id`) REFERENCES `servers` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `domains_mail_server_id_fk` FOREIGN KEY (`mail_server_id`) REFERENCES `servers` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `domains_web_server_id_fk` FOREIGN KEY (`web_server_id`) REFERENCES `servers` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getFailedLogins(): string
    {
        return "-- ----------------------------
-- Table structure for failed_logins
-- ----------------------------
DROP TABLE IF EXISTS `failed_logins`;
CREATE TABLE `failed_logins`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `session_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ip_address` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fl_session_id_idx`(`session_id`) USING BTREE,
  INDEX `fl_ip_address_idx`(`ip_address`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getFtpRepo(): string
    {
        return "-- ----------------------------
-- Table structure for ftp_repo
-- ----------------------------
DROP TABLE IF EXISTS `ftp_repo`;
CREATE TABLE `ftp_repo`  (
  `id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `client_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ftp_server` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ftp_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ftp_pass` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ftp_folder` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `ftpr_id_idx`(`id`) USING BTREE,
  INDEX `ftpr_client_id_idx`(`client_id`) USING BTREE,
  CONSTRAINT `ftp_repo_client_id_fk` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getFtpUsers(): string
    {
        return "-- ----------------------------
-- Table structure for ftp_users
-- ----------------------------
DROP TABLE IF EXISTS `ftp_users`;
CREATE TABLE `ftp_users`  (
  `id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `ftpu_id_idx`(`id`) USING BTREE,
  UNIQUE INDEX `ftpu_ftpname_idx`(`username`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getIpaddress(): string
    {
        return "-- ----------------------------
-- Table structure for ip_address
-- ----------------------------
DROP TABLE IF EXISTS `ip_address`;
CREATE TABLE `ip_address`  (
  `id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `index` int(10) UNSIGNED NULL DEFAULT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `old_data` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `ssl_status` tinyint(1) UNSIGNED NULL DEFAULT 0,
  `netmask` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `gateway` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `interface` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `format` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `is_default` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `server_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `init` tinyint(1) UNSIGNED NULL DEFAULT 1,
  `process` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `ipaddr_id_idx`(`id`) USING BTREE,
  INDEX `ipaddr_index_idx`(`index`) USING BTREE,
  INDEX `ipaddr_ip_address_idx`(`ip_address`) USING BTREE,
  INDEX `ipaddr_interface_idx`(`interface`) USING BTREE,
  INDEX `ipaddr_server_id_idx`(`server_id`) USING BTREE,
  CONSTRAINT `ip_address_server_id_fk` FOREIGN KEY (`server_id`) REFERENCES `servers` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getLetsencrypt(): string
    {
        return "-- ----------------------------
-- Table structure for letsencrypt
-- ----------------------------

";
    }

    public function getMailAlias(): string
    {
        return "-- ----------------------------
-- Table structure for mail_alias
-- ----------------------------
DROP TABLE IF EXISTS `mail_alias`;
CREATE TABLE `mail_alias`  (
  `id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `mail_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `alias_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `ma_id_idx`(`id`) USING BTREE,
  UNIQUE INDEX `ma_alias_name_idx`(`alias_name`) USING BTREE,
  INDEX `ma_mail_id_idx`(`mail_id`) USING BTREE,
  CONSTRAINT `mail_alias_mail_id_fk` FOREIGN KEY (`mail_id`) REFERENCES `mails` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getMailSettings(): string
    {
        return "-- ----------------------------
-- Table structure for mail_settings
-- ----------------------------
DROP TABLE IF EXISTS `mail_settings`;
CREATE TABLE `mail_settings`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `domain_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `nonexistend_user` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `mail_forward_addr` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `webmail` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `spamfilter` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `server_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `map_domain_id_idx`(`domain_id`) USING BTREE,
  UNIQUE INDEX `map_id_idx`(`id`) USING BTREE,
  INDEX `map_server_id_idx`(`server_id`) USING BTREE,
  CONSTRAINT `mail_settings_domain_id_fk` FOREIGN KEY (`domain_id`) REFERENCES `domains` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `mail_settings_server_id_fk` FOREIGN KEY (`server_id`) REFERENCES `servers` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getMails(): string
    {
        return "-- ----------------------------
-- Table structure for mails
-- ----------------------------
DROP TABLE IF EXISTS `mails`;
CREATE TABLE `mails`  (
  `id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `mail_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `domain_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `forward` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `forward_addr` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `mail_group` tinyint(1) UNSIGNED NULL DEFAULT 0,
  `autoresponder` tinyint(1) UNSIGNED NULL DEFAULT 0,
  `mailbox_quota` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `m_id_idx`(`id`) USING BTREE,
  UNIQUE INDEX `m_mail_name_idx`(`mail_name`, `domain_id`) USING BTREE,
  INDEX `m_domain_id_idx`(`domain_id`) USING BTREE,
  CONSTRAINT `mails_domain_id_fk` FOREIGN KEY (`domain_id`) REFERENCES `domains` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getMailServerSettings(): string
    {
        return "-- ----------------------------
-- Table structure for mailserver_settings
-- ----------------------------
DROP TABLE IF EXISTS `mailserver_settings`;
CREATE TABLE `mailserver_settings`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `data` longtext CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `server_type` tinyint(1) UNSIGNED NOT NULL,
  `server_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `smss_id_idx`(`id`) USING BTREE,
  INDEX `smss_server_id_idx`(`server_id`) USING BTREE,
  CONSTRAINT `mailserver_settings_server_id_fk` FOREIGN KEY (`server_id`) REFERENCES `servers` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getMessages(): string
    {
        return "-- ----------------------------
-- Table structure for messages
-- ----------------------------
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `message` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `type` tinyint(1) UNSIGNED NULL DEFAULT 0,
  `description` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `expire_date` datetime(0) NULL DEFAULT NULL,
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `smessages_id_idx`(`id`) USING BTREE,
  INDEX `smessages_status_idx`(`status`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getProtectDirectory(): string
    {
        return "-- ----------------------------
-- Table structure for protect_directory
-- ----------------------------
DROP TABLE IF EXISTS `protect_directory`;
CREATE TABLE `protect_directory`  (
  `id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `domain_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `path` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `header` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `subdomain_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0',
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `pd_id_idx`(`id`) USING BTREE,
  INDEX `pd_domain_id_idx`(`domain_id`) USING BTREE,
  INDEX `pd_path_idx`(`path`) USING BTREE,
  INDEX `pd_subdomain_id_idx`(`subdomain_id`) USING BTREE,
  CONSTRAINT `protected_directory_domain_id_fk` FOREIGN KEY (`domain_id`) REFERENCES `domains` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getProtectDirectoryUsers(): string
    {
        return "-- ----------------------------
-- Table structure for protect_directory_users
-- ----------------------------
DROP TABLE IF EXISTS `protect_directory_users`;
CREATE TABLE `protect_directory_users`  (
  `id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `protect_dir_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `pdu_id_idx`(`id`) USING BTREE,
  INDEX `pdu_username_idx`(`username`) USING BTREE,
  INDEX `pdu_protect_dir_id_idx`(`protect_dir_id`) USING BTREE,
  CONSTRAINT `pdu_protect_dir_id_fk` FOREIGN KEY (`protect_dir_id`) REFERENCES `protect_directory` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getScheduledTasks(): string
    {
        return "-- ----------------------------
-- Table structure for scheduled_tasks
-- ----------------------------
DROP TABLE IF EXISTS `scheduled_tasks`;
CREATE TABLE `scheduled_tasks`  (
  `id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `minute` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `hour` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `day` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `month` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `week` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `command` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `description` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `timestamp` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `php_script` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `domain_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0',
  `server_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `sct_id_idx`(`id`) USING BTREE,
  INDEX `sct_domain_id_idx`(`domain_id`) USING BTREE,
  INDEX `sct_type_idx`(`type`) USING BTREE,
  INDEX `scheduled_tasks_server_id_fk`(`server_id`) USING BTREE,
  CONSTRAINT `scheduled_tasks_domain_id_fk` FOREIGN KEY (`domain_id`) REFERENCES `domains` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `scheduled_tasks_server_id_fk` FOREIGN KEY (`server_id`) REFERENCES `servers` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getServerFtpRepo(): string
    {
        return "-- ----------------------------
-- Table structure for server_ftp_repo
-- ----------------------------
DROP TABLE IF EXISTS `server_ftp_repo`;
CREATE TABLE `server_ftp_repo`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ftp_server` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ftp_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ftp_pass` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ftp_folder` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `sftpr_id_idx`(`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getServerFtpSettings(): string
    {
        return "-- ----------------------------
-- Table structure for server_ftp_settings
-- ----------------------------
DROP TABLE IF EXISTS `server_ftp_settings`;
CREATE TABLE `server_ftp_settings`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `params` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `server_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `sftps_id_idx`(`id`) USING BTREE,
  INDEX `sftps_server_id_idx`(`server_id`) USING BTREE,
  CONSTRAINT `sfs_server_id_fk` FOREIGN KEY (`server_id`) REFERENCES `servers` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getServerLogSettings(): string
    {
        return "-- ----------------------------
-- Table structure for server_log_settings
-- ----------------------------
DROP TABLE IF EXISTS `server_log_settings`;
CREATE TABLE `server_log_settings`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `periodic_delete` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `store_day` int(10) UNSIGNED NOT NULL,
  `compressing` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `send_mail` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `server_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `sls_id_idx`(`id`) USING BTREE,
  UNIQUE INDEX `sls_server_id_idx`(`server_id`) USING BTREE,
  CONSTRAINT `sls_server_id_fk` FOREIGN KEY (`server_id`) REFERENCES `servers` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getServers(): string
    {
        return "-- ----------------------------
-- Table structure for servers
-- ----------------------------
DROP TABLE IF EXISTS `servers`;
CREATE TABLE `servers`  (
  `id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `description` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `is_core` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `os` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `type` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ip_address` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `domain_limit` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `database_limit` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `mail_limit` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `data` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `servers_id_idx`(`id`) USING BTREE,
  INDEX `servers_name_idx`(`name`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getServiceplans(): string
    {
        return "-- ----------------------------
-- Table structure for serviceplans
-- ----------------------------
DROP TABLE IF EXISTS `serviceplans`;
CREATE TABLE `serviceplans`  (
  `id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `client_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_default` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `expire_time` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'unlimited',
  `client_limit` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'unlimited',
  `domain_limit` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'unlimited',
  `disk_limit` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'unlimited',
  `traffic_limit` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'unlimited',
  `subdomain_limit` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'unlimited',
  `domain_alias_limit` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'unlimited',
  `email_box_limit` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'unlimited',
  `email_box_size_limit` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'unlimited',
  `email_forward_limit` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'unlimited',
  `email_groups_limit` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'unlimited',
  `email_autoresponder_limit` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'unlimited',
  `email_lists_limit` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'unlimited',
  `database_limit` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'unlimited',
  `database_size_limit` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'unlimited',
  `template_limit` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'unlimited',
  `access_panel` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `man_client` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `man_domain` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `man_subdomain` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `man_domain_alias` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `man_email_alias` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `man_hosting_settings` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `man_task` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `man_dns` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `man_mailing_lists` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `man_spam_filters` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `man_backup` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `man_email` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `man_database` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `man_protect_dir` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `man_ssl` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `man_template` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `overuse` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `php` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `php_version` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `cgi` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `perl` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `python` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `fast_cgi` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `statistic_support` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `statistic_type` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `asp` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `aspnet` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `aspnet_core` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `aspnet_version` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `dedicated_pool` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `max_cpu` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `application_pool_mode` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `ssl` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `sp_id_idx`(`id`) USING BTREE,
  UNIQUE INDEX `sp_client_id_name_idx`(`name`, `client_id`) USING BTREE,
  INDEX `sp_name_idx`(`name`) USING BTREE,
  INDEX `sp_client_id_idx`(`client_id`) USING BTREE,
  CONSTRAINT `serviceplans_client_id_fk` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getSettings(): string
    {
        return "-- ----------------------------
-- Table structure for settings
-- ----------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `auto_update` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `update_time` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `update_server` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `random_time` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `server_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `settings_id_idx`(`id`) USING BTREE,
  INDEX `settings_server_id_idx`(`server_id`) USING BTREE,
  CONSTRAINT `settings_server_id_fk` FOREIGN KEY (`server_id`) REFERENCES `servers` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getSpamList(): string
    {
        return "-- ----------------------------
-- Table structure for spam_list
-- ----------------------------
DROP TABLE IF EXISTS `spam_list`;
CREATE TABLE `spam_list`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `rule` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `server_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `sys_spaml_id_idx`(`id`) USING BTREE,
  INDEX `sys_spaml_rule_idx`(`rule`) USING BTREE,
  INDEX `sys_spaml_server_id_idx`(`server_id`) USING BTREE,
  INDEX `sys_spaml_type_idx`(`type`) USING BTREE,
  CONSTRAINT `spam_list_server_id_fk` FOREIGN KEY (`server_id`) REFERENCES `servers` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getSpamSettings(): string
    {
        return "-- ----------------------------
-- Table structure for spam_settings
-- ----------------------------
DROP TABLE IF EXISTS `spam_settings`;
CREATE TABLE `spam_settings`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `scores` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `pyzor` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `razor` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `skip_rbl` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `bayes` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `spam_report` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `use_bayes_rules` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `bayes_auto_learn_on_error` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `bayes_auto_learn` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `bayes_auto_learn_threshold_nonspam` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0',
  `bayes_auto_learn_threshold_spam` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0',
  `rewrite_header` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `server_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `installed` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `sys_spam_settings_id_idx`(`id`) USING BTREE,
  INDEX `sys_spam_settings_status_idx`(`status`) USING BTREE,
  INDEX `sys_spam_settings_server_id_idx`(`server_id`) USING BTREE,
  CONSTRAINT `spam_settings_server_id_fk` FOREIGN KEY (`server_id`) REFERENCES `servers` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getSubdomains(): string
    {
        return "-- ----------------------------
-- Table structure for subdomains
-- ----------------------------
DROP TABLE IF EXISTS `subdomains`;
CREATE TABLE `subdomains`  (
  `id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `domain_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ipaddr_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `subdomain_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `full_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ftpuser_type` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `ftpuser_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ssl` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `php` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `php_version` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `cgi` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `perl` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `python` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `fast_cgi` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `asp` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `aspnet_core` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `aspnet` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `aspnet_version` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `dedicated_pool` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `max_cpu` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `custom_error` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `subdomains_id_index`(`id`) USING BTREE,
  INDEX `subdomains_domain_id_idx`(`domain_id`) USING BTREE,
  INDEX `subdomains_ipaddr_id_idx`(`ipaddr_id`) USING BTREE,
  INDEX `subdomains_subdomain_name_idx`(`subdomain_name`) USING BTREE,
  INDEX `subdomains_ftpuser_id_fk`(`ftpuser_id`) USING BTREE,
  CONSTRAINT `subdomains_domain_id_fk` FOREIGN KEY (`domain_id`) REFERENCES `domains` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `subdomains_ipaddr_id_fk` FOREIGN KEY (`ipaddr_id`) REFERENCES `ip_address` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `subdomains_ftpuser_id_fk` FOREIGN KEY (`ftpuser_id`) REFERENCES `ftp_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getSuccessLogins(): string
    {
        return "-- ----------------------------
-- Table structure for success_logins
-- ----------------------------
DROP TABLE IF EXISTS `success_logins`;
CREATE TABLE `success_logins`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `mail_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `session` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `sl_id_idx`(`id`) USING BTREE,
  INDEX `sl_user_id_idx`(`user_id`) USING BTREE,
  INDEX `sl_mail_id_idx`(`mail_id`) USING BTREE,
  INDEX `sl_ip_address_idx`(`ip_address`) USING BTREE,
  CONSTRAINT `success_logins_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `success_logins_mail_id_fk` FOREIGN KEY (`mail_id`) REFERENCES `mails` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getSupport(): string
    {
        return "-- ----------------------------
-- Table structure for support
-- ----------------------------
DROP TABLE IF EXISTS `support`;
CREATE TABLE `support`  (
  `id` int(20) NOT NULL,
  `request_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `request_user_email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `description` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `closed_date` datetime(0) NULL DEFAULT NULL,
  `user` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `user_msg` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `connecting_status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `disable_clients_logins` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `support_id_idx`(`id`) USING BTREE,
  INDEX `support_request_user_idx`(`request_user`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getSystemPackages(): string
    {
        return "-- ----------------------------
-- Table structure for system_packages
-- ----------------------------
DROP TABLE IF EXISTS `system_packages`;
CREATE TABLE `system_packages`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `package_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `package_desc` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `version` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `support` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `server_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `spst_id_idx`(`id`) USING BTREE,
  INDEX `spst_server_id_idx`(`server_id`) USING BTREE,
  INDEX `spst_package_name`(`package_name`) USING BTREE,
  CONSTRAINT `system_packages_server_id_fk` FOREIGN KEY (`server_id`) REFERENCES `servers` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getSystemSettings(): string
    {
        return "-- ----------------------------
-- Table structure for system_settings
-- ----------------------------
DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE `system_settings`  (
  `key` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `value` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  PRIMARY KEY (`key`) USING BTREE,
  UNIQUE INDEX `misc_key`(`key`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getSystemVersions(): string
    {
        return "-- ----------------------------
-- Table structure for system_versions
-- ----------------------------
DROP TABLE IF EXISTS `system_versions`;
CREATE TABLE `system_versions`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `version` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getUpdates(): string
    {
        return "-- ----------------------------
-- Table structure for updates
-- ----------------------------
DROP TABLE IF EXISTS `updates`;
CREATE TABLE `updates`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `package` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `version` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `description` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `server_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `update_id_idx`(`id`) USING BTREE,
  INDEX `update_server_id_idx`(`server_id`) USING BTREE,
  CONSTRAINT `updates_server_id_fk` FOREIGN KEY (`server_id`) REFERENCES `servers` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getUserdata(): string
    {
        return "-- ----------------------------
-- Table structure for userdata
-- ----------------------------
DROP TABLE IF EXISTS `userdata`;
CREATE TABLE `userdata`  (
  `user_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `fullname` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `company` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `fax` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `telephone` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `city` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `region` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `zipcode` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `country` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  `locked` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`) USING BTREE,
  CONSTRAINT `userdata_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getUsers(): string
    {
        return "-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `password_last_set` datetime(0) NULL DEFAULT NULL,
  `last_login` datetime(0) NULL DEFAULT NULL,
  `last_login_ip` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `parent` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `type` tinyint(1) UNSIGNED NOT NULL,
  `must_change_password` tinyint(1) UNSIGNED NULL DEFAULT 0,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  `expire_date` datetime(0) NULL DEFAULT NULL,
  `status_msg` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `interface` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `language` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `api_status` tinyint(1) NOT NULL DEFAULT 0,
  `changed_data` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `locked` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `users_id_idx`(`id`) USING BTREE,
  UNIQUE INDEX `users_username_idx`(`username`) USING BTREE,
  INDEX `users_parent_idx`(`parent`) USING BTREE,
  INDEX `users_type_idx`(`type`) USING BTREE,
  INDEX `users_api_status_idx`(`api_status`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getVirtualDirectory(): string
    {
        return "-- ----------------------------
-- Table structure for virtual_directory
-- ----------------------------
DROP TABLE IF EXISTS `virtual_directory`;
CREATE TABLE `virtual_directory`  (
  `id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `domain_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `path` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `app` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `vd_id_idx`(`id`) USING BTREE,
  INDEX `vd_domain_id_idx`(`domain_id`) USING BTREE,
  INDEX `vd_name_idx`(`name`) USING BTREE,
  CONSTRAINT `virtual_directory_domain_id_fk` FOREIGN KEY (`domain_id`) REFERENCES `domains` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    public function getWebServer(): string
    {
        return "-- ----------------------------
-- Table structure for webserver
-- ----------------------------
DROP TABLE IF EXISTS `webserver`;
CREATE TABLE `webserver`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `php_status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `php_versions` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `perl_status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `python_status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `server_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_date` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `sys_webs_id_idx`(`id`) USING BTREE,
  INDEX `sys_webs_server_id_idx`(`server_id`) USING BTREE,
  CONSTRAINT `webserver_server_id_fk` FOREIGN KEY (`server_id`) REFERENCES `servers` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
";
    }

    private function triggers(): string
    {
        return "
        -- ----------------------------
-- Triggers structure for table userdata
-- ----------------------------
DROP TRIGGER IF EXISTS `root_userdata_del`;
delimiter ;;
CREATE TRIGGER `root_userdata_del` BEFORE DELETE ON `userdata` FOR EACH ROW IF OLD.locked THEN
  SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cannot delete locked record';
END IF
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table users
-- ----------------------------
DROP TRIGGER IF EXISTS `root_del`;
delimiter ;;
CREATE TRIGGER `root_del` BEFORE DELETE ON `users` FOR EACH ROW IF OLD.locked THEN
  SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cannot delete locked record';
END IF
;;
delimiter ;
";
    }

    public function getTelegram(): string
    {
        return "-- ----------------------------
-- Table structure for telegram
-- ----------------------------
DROP TABLE IF EXISTS `telegram`;
CREATE TABLE `telegram`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `api_key` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `bot_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `settings` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci;
";
    }

    public function getTelegramUsers(): string
    {
        return "-- ----------------------------
-- Table structure for telegram_users
-- ----------------------------
DROP TABLE IF EXISTS `telegram_users`;
CREATE TABLE `telegram_users`  (
  `chat_id` int(10) UNSIGNED NOT NULL,
  `language` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `fullname` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `activation_code` int(7) UNSIGNED NOT NULL,
  `status` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `created_date` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`chat_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci;
";
    }

    public function mysql($part): string
    {
        $includes = $part . $this->triggers();
        $db_name = DATABASE_NAME;
        return " use $db_name;
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
$includes
SET FOREIGN_KEY_CHECKS = 1;";
    }
}