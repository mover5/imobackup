<?php
/**
 * FURL Templates cache. Do not attempt to modify this file.
 * Please modify the relevant 'furlTemplates.php' file in /{app}/extensions/furlTemplates.php
 * and rebuild from the Admin CP
 *
 * Written: Tue, 28 Jan 2014 06:36:02 +0000
 *
 * Why? Because Matt says so.
 */
 $templates = array (
  '__data__' => 
  array (
    'start' => '-',
    'end' => '/',
    'varBlock' => '?',
    'varPage' => 'page-',
    'varSep' => '&',
    'varJoin' => '=',
  ),
  'section=register' => 
  array (
    'app' => 'core',
    'allowRedirect' => 0,
    'out' => 
    array (
      0 => '#app=core(&amp;|&)module=global(&amp;|&)section=register(&amp;|&|$)#i',
      1 => 'register/$3',
    ),
    'in' => 
    array (
      'regex' => '#/register(/|$|\\?)#i',
      'matches' => 
      array (
        0 => 
        array (
          0 => 'app',
          1 => 'core',
        ),
        1 => 
        array (
          0 => 'module',
          1 => 'global',
        ),
        2 => 
        array (
          0 => 'section',
          1 => 'register',
        ),
      ),
    ),
  ),
  'tags' => 
  array (
    'app' => 'core',
    'isPagesMode' => true,
    'allowRedirect' => 0,
    'out' => 
    array (
      0 => '#app=core(&amp;|&)module=search(&amp;|&)do=search(&amp;|&)search_tags=(\\S+?)(&amp;|&)search_app=(\\S+?)(&amp;|&|$)#i',
      1 => 'tags/$6/$4/$7',
    ),
    'in' => 
    array (
      'regex' => '#/tags/(\\S+?)/(\\S+?)/#i',
      'matches' => 
      array (
        0 => 
        array (
          0 => 'app',
          1 => 'core',
        ),
        1 => 
        array (
          0 => 'module',
          1 => 'search',
        ),
        2 => 
        array (
          0 => 'do',
          1 => 'search',
        ),
        3 => 
        array (
          0 => 'search_tags',
          1 => '$2',
        ),
        4 => 
        array (
          0 => 'search_app',
          1 => '$1',
        ),
      ),
    ),
  ),
  'privacy' => 
  array (
    'app' => 'core',
    'allowRedirect' => 0,
    'out' => 
    array (
      0 => '#app=core(&amp;|&)module=global(&amp;|&)section=privacy(&amp;|&|$)#i',
      1 => 'privacypolicy/$4/',
    ),
    'in' => 
    array (
      'regex' => '#/privacypolicy/#i',
      'matches' => 
      array (
        0 => 
        array (
          0 => 'app',
          1 => 'core',
        ),
        1 => 
        array (
          0 => 'module',
          1 => 'global',
        ),
        2 => 
        array (
          0 => 'section',
          1 => 'privacy',
        ),
      ),
    ),
  ),
  'likeunsubscribe' => 
  array (
    'app' => 'core',
    'allowRedirect' => 0,
    'out' => 
    array (
      0 => '#app=core(&amp;|&)module=global(&amp;|&)section=like(&amp;|&)do=unsubscribe(&amp;|&)key=(\\S+?)(&amp;|&|$)#i',
      1 => 'unsubscribe/$5/',
    ),
    'in' => 
    array (
      'regex' => '#/unsubscribe/(\\S+?)/$#i',
      'matches' => 
      array (
        0 => 
        array (
          0 => 'app',
          1 => 'core',
        ),
        1 => 
        array (
          0 => 'module',
          1 => 'global',
        ),
        2 => 
        array (
          0 => 'section',
          1 => 'like',
        ),
        3 => 
        array (
          0 => 'do',
          1 => 'unsubscribe',
        ),
        4 => 
        array (
          0 => 'key',
          1 => '$1',
        ),
      ),
    ),
  ),
  'findcomment' => 
  array (
    'app' => 'core',
    'allowRedirect' => 0,
    'out' => 
    array (
      0 => '#app=core(&amp;|&)module=global(&amp;|&)section=comments(&amp;|&)do=findComment(&amp;|&)fromApp=(\\S+?)(&amp;|&)parentId=(\\d+?)(&amp;|&)commentId=(\\d+?)(&amp;|&|$)#i',
      1 => 'findComment/$5/$7-$9',
    ),
    'in' => 
    array (
      'regex' => '#/findComment/(\\S+?-\\S+?)/(\\d+?)-(\\d+?)$#i',
      'matches' => 
      array (
        0 => 
        array (
          0 => 'app',
          1 => 'core',
        ),
        1 => 
        array (
          0 => 'module',
          1 => 'global',
        ),
        2 => 
        array (
          0 => 'section',
          1 => 'comments',
        ),
        3 => 
        array (
          0 => 'do',
          1 => 'findComment',
        ),
        4 => 
        array (
          0 => 'fromApp',
          1 => '$1',
        ),
        5 => 
        array (
          0 => 'parentId',
          1 => '$2',
        ),
        6 => 
        array (
          0 => 'commentId',
          1 => '$3',
        ),
      ),
    ),
  ),
  'section=rss' => 
  array (
    'app' => 'core',
    'allowRedirect' => 0,
    'out' => 
    array (
      0 => '#app=core(&amp;|&)module=global(&amp;|&)section=rss(&amp;|&)type=(\\w+?)$#i',
      1 => 'rss/$4/',
    ),
    'in' => 
    array (
      'regex' => '#/rss/(\\w+?)/$#i',
      'matches' => 
      array (
        0 => 
        array (
          0 => 'app',
          1 => 'core',
        ),
        1 => 
        array (
          0 => 'module',
          1 => 'global',
        ),
        2 => 
        array (
          0 => 'section',
          1 => 'rss',
        ),
        3 => 
        array (
          0 => 'type',
          1 => '$1',
        ),
      ),
    ),
  ),
  'section=rss2' => 
  array (
    'app' => 'core',
    'allowRedirect' => 0,
    'out' => 
    array (
      0 => '#app=core(&amp;|&)module=global(&amp;|&)section=rss(&amp;|&)type=(\\w+?)(&amp;|&)id=(\\w+?)$#i',
      1 => 'rss/$4/$6-#{__title__}/',
    ),
    'in' => 
    array (
      'regex' => '#/rss/(\\w+?)/(\\w+?)-#i',
      'matches' => 
      array (
        0 => 
        array (
          0 => 'app',
          1 => 'core',
        ),
        1 => 
        array (
          0 => 'module',
          1 => 'global',
        ),
        2 => 
        array (
          0 => 'section',
          1 => 'rss',
        ),
        3 => 
        array (
          0 => 'type',
          1 => '$1',
        ),
        4 => 
        array (
          0 => 'id',
          1 => '$2',
        ),
      ),
    ),
  ),
  'showannouncement' => 
  array (
    'app' => 'forums',
    'allowRedirect' => 1,
    'out' => 
    array (
      0 => '#showannouncement=(.+?)((?:&|&amp;)f=(.+?))?(&|$)#i',
      1 => 'forum-$3/announcement-$1-#{__title__}/$4',
    ),
    'in' => 
    array (
      'regex' => '#/forum-(\\d+?)?/announcement-(\\d+?)-#i',
      'matches' => 
      array (
        0 => 
        array (
          0 => 'showannouncement',
          1 => '$2',
        ),
        1 => 
        array (
          0 => 'f',
          1 => '$1',
        ),
      ),
    ),
  ),
  'showforum' => 
  array (
    'app' => 'forums',
    'allowRedirect' => 1,
    'isPagesMode' => 1,
    'out' => 
    array (
      0 => '#showforum=(.+?)(&|$)#i',
      1 => 'forum/$1-#{__title__}/$2',
    ),
    'in' => 
    array (
      'regex' => '#^/forum/(\\d+?)-#i',
      'matches' => 
      array (
        0 => 
        array (
          0 => 'showforum',
          1 => '$1',
        ),
      ),
    ),
  ),
  'showtopic' => 
  array (
    'app' => 'forums',
    'allowRedirect' => 1,
    'isPagesMode' => 1,
    'out' => 
    array (
      0 => '#showtopic=(.+?)(\\#|&|$)#i',
      1 => 'topic/$1-#{__title__}/$2',
    ),
    'in' => 
    array (
      'regex' => '#^/topic/(\\d+?)-#i',
      'matches' => 
      array (
        0 => 
        array (
          0 => 'showtopic',
          1 => '$1',
        ),
      ),
    ),
  ),
  'acteqst' => 
  array (
    'app' => 'forums',
    'allowRedirect' => 1,
    'out' => 
    array (
      0 => '#act=ST(.*?)&t=(.+?)(&|$)#i',
      1 => 'topic/$2-#{__title__}/$3',
    ),
    'in' => 
    array (
      'regex' => '#^notavalidrequest$#i',
      'matches' => 
      array (
        0 => 
        array (
          0 => 'showtopic',
          1 => '0',
        ),
      ),
    ),
  ),
  'act=idx' => 
  array (
    'app' => 'forums',
    'allowRedirect' => 0,
    'out' => 
    array (
      0 => '#act=idx(&|$)#i',
      1 => 'index$1',
    ),
    'in' => 
    array (
      'regex' => '#^/index(/|$|\\?)#i',
      'matches' => 
      array (
        0 => 
        array (
          0 => 'act',
          1 => 'idx',
        ),
      ),
    ),
  ),
  'showuser' => 
  array (
    'app' => 'members',
    'allowRedirect' => 1,
    'out' => 
    array (
      0 => '#showuser=(.+?)((?:&|&amp;)f=(.+?))?(&|$)#i',
      1 => 'user/$1-#{__title__}/$2$4',
    ),
    'in' => 
    array (
      'regex' => '#^/user/(\\d+?)-#i',
      'matches' => 
      array (
        0 => 
        array (
          0 => 'showuser',
          1 => '$1',
        ),
      ),
    ),
  ),
  'members_status_legacy' => 
  array (
    'app' => 'members',
    'allowRedirect' => 0,
    'out' => 
    array (
      0 => '#app=xxxxx(?:&|&amp;)module=profile(?:&|&amp;)section=status(?:&|&amp;)type=single(&|$)#i',
      1 => 'statuses/user/#{__title-0__}-#{__title-1__}/$1',
    ),
    'newTemplate' => 'members_status_single',
    'in' => 
    array (
      'regex' => '#^/statuses/id/(\\d+?)(/|$|\\?)#i',
      'matches' => 
      array (
        0 => 
        array (
          0 => 'app',
          1 => 'members',
        ),
        1 => 
        array (
          0 => 'module',
          1 => 'profile',
        ),
        2 => 
        array (
          0 => 'section',
          1 => 'status',
        ),
        3 => 
        array (
          0 => 'type',
          1 => 'single',
        ),
        4 => 
        array (
          0 => 'status_id',
          1 => '$1',
        ),
      ),
    ),
  ),
  'members_status_single' => 
  array (
    'app' => 'members',
    'allowRedirect' => 0,
    'out' => 
    array (
      0 => '#app=members(?:&|&amp;)module=profile(?:&|&amp;)section=status(?:&|&amp;)type=single(&|$)#i',
      1 => 'statuses/user/#{__title-0__}-#{__title-1__}/$1',
    ),
    'in' => 
    array (
      'regex' => '#^/statuses/user/([^/]+?)/\\{__varBlock__\\}status_id#i',
      'matches' => 
      array (
        0 => 
        array (
          0 => 'app',
          1 => 'members',
        ),
        1 => 
        array (
          0 => 'module',
          1 => 'profile',
        ),
        2 => 
        array (
          0 => 'section',
          1 => 'status',
        ),
        3 => 
        array (
          0 => 'type',
          1 => 'single',
        ),
      ),
    ),
  ),
  'members_status_member_all' => 
  array (
    'app' => 'members',
    'allowRedirect' => 0,
    'out' => 
    array (
      0 => '#app=members(?:&|&amp;)module=profile(?:&|&amp;)section=status(?:&|&amp;)type=memberall(?:&|&amp;)member_id=\\d+?(&|$)#i',
      1 => 'statuses/user/#{__title-0__}-#{__title-1__}/$1',
    ),
    'in' => 
    array (
      'regex' => '#^/statuses/user/(\\d+?)-([^/]+?)/(?!\\#\\{__varBlock__\\}status_id)#i',
      'matches' => 
      array (
        0 => 
        array (
          0 => 'app',
          1 => 'members',
        ),
        1 => 
        array (
          0 => 'module',
          1 => 'profile',
        ),
        2 => 
        array (
          0 => 'section',
          1 => 'status',
        ),
        3 => 
        array (
          0 => 'member_id',
          1 => '$1',
        ),
      ),
    ),
  ),
  'members_status_friends' => 
  array (
    'app' => 'members',
    'allowRedirect' => 0,
    'out' => 
    array (
      0 => '#app=members(?:&|&amp;)module=profile(?:&|&amp;)section=status(?:&|&amp;)type=friends(&|$)#i',
      1 => 'statuses/friends/$2',
    ),
    'in' => 
    array (
      'regex' => '#^/statuses/friends#i',
      'matches' => 
      array (
        0 => 
        array (
          0 => 'app',
          1 => 'members',
        ),
        1 => 
        array (
          0 => 'module',
          1 => 'profile',
        ),
        2 => 
        array (
          0 => 'section',
          1 => 'status',
        ),
        3 => 
        array (
          0 => 'type',
          1 => 'friends',
        ),
      ),
    ),
  ),
  'members_status_all' => 
  array (
    'app' => 'members',
    'allowRedirect' => 0,
    'out' => 
    array (
      0 => '#app=members(?:&|&amp;)module=profile(?:&|&amp;)section=status((?:&|&amp;)type=all)?(&|$)#i',
      1 => 'statuses/all/$2',
    ),
    'in' => 
    array (
      'regex' => '#^/statuses/all#i',
      'matches' => 
      array (
        0 => 
        array (
          0 => 'app',
          1 => 'members',
        ),
        1 => 
        array (
          0 => 'module',
          1 => 'profile',
        ),
        2 => 
        array (
          0 => 'section',
          1 => 'status',
        ),
      ),
    ),
  ),
  'members_list' => 
  array (
    'app' => 'members',
    'allowRedirect' => 0,
    'out' => 
    array (
      0 => '#app=members((&|&amp;)module=list)?#i',
      1 => 'members/',
    ),
    'in' => 
    array (
      'regex' => '#^/members(/|$|\\?)#i',
      'matches' => 
      array (
        0 => 
        array (
          0 => 'app',
          1 => 'members',
        ),
        1 => 
        array (
          0 => 'module',
          1 => 'list',
        ),
      ),
    ),
  ),
  'most_liked' => 
  array (
    'app' => 'members',
    'allowRedirect' => 0,
    'out' => 
    array (
      0 => '#app=members(?:&|&amp;)module=reputation(?:&|&amp;)section=most#i',
      1 => 'best-content/',
    ),
    'in' => 
    array (
      'regex' => '#^/best-content(/|$|\\?)#i',
      'matches' => 
      array (
        0 => 
        array (
          0 => 'app',
          1 => 'members',
        ),
        1 => 
        array (
          0 => 'module',
          1 => 'reputation',
        ),
        2 => 
        array (
          0 => 'section',
          1 => 'most',
        ),
      ),
    ),
  ),
  'app=ipchat' => 
  array (
    'app' => 'ipchat',
    'allowRedirect' => 1,
    'out' => 
    array (
      0 => '/(?<!from)app=ipchat/i',
      1 => 'chat/',
    ),
    'in' => 
    array (
      'regex' => '#^/chat(/|$|\\?)#i',
      'matches' => 
      array (
        0 => 
        array (
          0 => 'app',
          1 => 'ipchat',
        ),
      ),
    ),
  ),
  'cal_event' => 
  array (
    'app' => 'calendar',
    'allowRedirect' => 1,
    'out' => 
    array (
      0 => '#app=calendar(?:&|&amp;)module=calendar(?:&|&amp;)section=view(?:&|&amp;)do=showevent(?:&|&amp;)event_id=(\\d+?)(&|$)#i',
      1 => 'calendar/event/$1-#{__title__}/$2',
    ),
    'in' => 
    array (
      'regex' => '#/calendar/event/(\\d+?)-([^/]+?)(/|$)#i',
      'matches' => 
      array (
        0 => 
        array (
          0 => 'app',
          1 => 'calendar',
        ),
        1 => 
        array (
          0 => 'module',
          1 => 'calendar',
        ),
        2 => 
        array (
          0 => 'section',
          1 => 'view',
        ),
        3 => 
        array (
          0 => 'do',
          1 => 'showevent',
        ),
        4 => 
        array (
          0 => 'event_id',
          1 => '$1',
        ),
      ),
    ),
  ),
  'cal_post' => 
  array (
    'app' => 'calendar',
    'allowRedirect' => 1,
    'out' => 
    array (
      0 => '#app=calendar(?:&|&amp;)module=calendar(?:&|&amp;)section=post(?:&|&amp;)cal_id=(.+?)(?:&|&amp;)do=newevent#i',
      1 => 'calendar/$1-#{__title__}/add',
    ),
    'in' => 
    array (
      'regex' => '#/calendar/(\\d+?)-([^/]+?)/add(/|$|\\?)#i',
      'matches' => 
      array (
        0 => 
        array (
          0 => 'app',
          1 => 'calendar',
        ),
        1 => 
        array (
          0 => 'module',
          1 => 'calendar',
        ),
        2 => 
        array (
          0 => 'section',
          1 => 'post',
        ),
        3 => 
        array (
          0 => 'do',
          1 => 'newevent',
        ),
        4 => 
        array (
          0 => 'cal_id',
          1 => '$1',
        ),
      ),
    ),
  ),
  'cal_day' => 
  array (
    'app' => 'calendar',
    'allowRedirect' => 1,
    'out' => 
    array (
      0 => '#app=calendar(?:&|&amp;)module=calendar(?:&|&amp;)section=view(?:&|&amp;)cal_id=(.+?)(?:&|&amp;)do=showday(?:&|&amp;)y=(.+?)(?:&|&amp;)m=(.+?)(?:&|&amp;)d=(.+?)(&|$)#i',
      1 => 'calendar/$1-#{__title__}/day-$2-$3-$4$5',
    ),
    'in' => 
    array (
      'regex' => '#/calendar/(\\d+?)-([^/]+?)/day-(\\d+?)-(\\d+?)-(\\d+?)(/|$)#i',
      'matches' => 
      array (
        0 => 
        array (
          0 => 'app',
          1 => 'calendar',
        ),
        1 => 
        array (
          0 => 'module',
          1 => 'calendar',
        ),
        2 => 
        array (
          0 => 'section',
          1 => 'view',
        ),
        3 => 
        array (
          0 => 'do',
          1 => 'showday',
        ),
        4 => 
        array (
          0 => 'cal_id',
          1 => '$1',
        ),
        5 => 
        array (
          0 => 'y',
          1 => '$3',
        ),
        6 => 
        array (
          0 => 'm',
          1 => '$4',
        ),
        7 => 
        array (
          0 => 'd',
          1 => '$5',
        ),
      ),
    ),
  ),
  'cal_week' => 
  array (
    'app' => 'calendar',
    'allowRedirect' => 1,
    'out' => 
    array (
      0 => '#app=calendar(?:&|&amp;)module=calendar(?:&|&amp;)section=view(?:&|&amp;)cal_id=(\\d+?)(?:&|&amp;)do=showweek(?:&|&amp;)week=(.+?)(?:&|$)#i',
      1 => 'calendar/$1-#{__title__}/week-$2',
    ),
    'in' => 
    array (
      'regex' => '#/calendar/(\\d+?)-([^/]+?)/week-(.+?)(/|$)#i',
      'matches' => 
      array (
        0 => 
        array (
          0 => 'app',
          1 => 'calendar',
        ),
        1 => 
        array (
          0 => 'module',
          1 => 'calendar',
        ),
        2 => 
        array (
          0 => 'section',
          1 => 'view',
        ),
        3 => 
        array (
          0 => 'do',
          1 => 'showweek',
        ),
        4 => 
        array (
          0 => 'cal_id',
          1 => '$1',
        ),
        5 => 
        array (
          0 => 'week',
          1 => '$3',
        ),
      ),
    ),
  ),
  'cal_month' => 
  array (
    'app' => 'calendar',
    'allowRedirect' => 1,
    'out' => 
    array (
      0 => '#app=calendar(?:&|&amp;)module=calendar(?:&|&amp;)section=view(?:&|&amp;)cal_id=(.+?)(?:&|&amp;)m=(.+?)(?:&|&amp;)y=(.+?)(?:&|$)#i',
      1 => 'calendar/$1-#{__title__}/$2-$3',
    ),
    'in' => 
    array (
      'regex' => '#/calendar/(\\d+?)-([^/]+?)/(\\d+?)-(\\d+?)(/|$)#i',
      'matches' => 
      array (
        0 => 
        array (
          0 => 'app',
          1 => 'calendar',
        ),
        1 => 
        array (
          0 => 'module',
          1 => 'calendar',
        ),
        2 => 
        array (
          0 => 'section',
          1 => 'view',
        ),
        3 => 
        array (
          0 => 'cal_id',
          1 => '$1',
        ),
        4 => 
        array (
          0 => 'm',
          1 => '$3',
        ),
        5 => 
        array (
          0 => 'y',
          1 => '$4',
        ),
      ),
    ),
  ),
  'cal_calendar' => 
  array (
    'app' => 'calendar',
    'allowRedirect' => 1,
    'out' => 
    array (
      0 => '#app=calendar(?:&|&amp;)module=calendar(?:&|&amp;)section=view(?:&|&amp;)cal_id=(.+?)(&|$)#i',
      1 => 'calendar/$1-#{__title__}',
    ),
    'in' => 
    array (
      'regex' => '#/calendar/(\\d+?)-([^/]+?)(/|$)#i',
      'matches' => 
      array (
        0 => 
        array (
          0 => 'app',
          1 => 'calendar',
        ),
        1 => 
        array (
          0 => 'module',
          1 => 'calendar',
        ),
        2 => 
        array (
          0 => 'section',
          1 => 'view',
        ),
        3 => 
        array (
          0 => 'cal_id',
          1 => '$1',
        ),
      ),
    ),
  ),
  'app=calendar' => 
  array (
    'app' => 'calendar',
    'allowRedirect' => 1,
    'out' => 
    array (
      0 => '#app=calendar$#i',
      1 => 'calendar/',
    ),
    'in' => 
    array (
      'regex' => '#/calendar(/|$|\\?)#i',
      'matches' => 
      array (
        0 => 
        array (
          0 => 'app',
          1 => 'calendar',
        ),
      ),
    ),
  ),
);

?>