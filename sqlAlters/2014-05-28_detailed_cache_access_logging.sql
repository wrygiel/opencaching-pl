-- 2014-05-28 Detailed cache access logging
-- @author: Bogus z Polska

create table CACHE_ACCESS_LOGS (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  event_date datetime NOT NULL,
  cache_id int(11) NOT NULL,
  user_id int(11),
  source varchar(2) not null comment 'B - browser - main opencaching site, M - mobile, O - okapi',
  event varchar(32) not null comment 'viewcache, viewlogs, ... ',
  ip_addr varchar(32) not null comment 'request IP',
  user_agent varchar(128) comment 'User-Agent HTTP header',
  forwarded_for varchar(128) comment 'X-Forwarded-For HTTP header',
  PRIMARY KEY (id),
  KEY access_logs_cache_id (cache_id),
  KEY access_logs_user_id (user_id)
) 
ENGINE=InnoDB DEFAULT CHARSET=utf8;
