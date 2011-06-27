TRUNCATE TABLE acos;
INSERT INTO `acos` (`aco_id`, `obj_id`, `model`, `lft`, `rgt`, `parent_id`) VALUES
(1, 1, 'group_info', 1, 4, NULL),
(2, 1, 'user_info', 2, 3, 1);
TRUNCATE TABLE aros;
INSERT INTO `aros` (`aro_id`, `obj_id`, `model`, `lft`, `rgt`, `parent_id`) VALUES
(1, 1, 'group_info', 1, 4, NULL),
(2, 1, 'user_info', 2, 3, 1);
TRUNCATE TABLE aros_acos;
INSERT INTO `aros_acos` (`perm_id`, `aro_id`, `aco_id`, `create`, `read`, `update`, `delete`) VALUES
(1, 2, 1, 'allow', 'allow', 'allow', 'allow');
TRUNCATE TABLE group_info;
INSERT INTO `group_info` (`grp_id`, `grp_descr`) VALUES
(1, 'root');
TRUNCATE TABLE user_info;
INSERT INTO `user_info` (`usr_id`, `usr_login`, `usr_password`, `usr_name`, `usr_settings`) VALUES
(1, 'pfbiasuz', '202cb962ac59075b964b07152d234b70', 'Pietro Biasuz', 'date_format=dd/mm/yyyy;language=pt_BR.utf8');
TRUNCATE TABLE user_group;
INSERT INTO `user_group` (`usr_id`, `grp_id`) VALUES
(1, 1);
TRUNCATE TABLE request_info;

