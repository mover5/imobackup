#!/usr/bin/perl -w

# IkonBoard Redirect Scripts

# This file serves as a "gateway" in the old IkonBoard location to pass parameters from
# Perl to it's sibling PHP Gateway to allow for redirection from IkonBoard to Invision Power Board
# @author Ryan "Oh my god I'm writing in Perl" Ashbrook

use CGI::Carp "fatalsToBrowser";

require CGI;

$CGI = new CGI;

$URL_TO_IPB = "http://www.example.com/forums";

print "Status: 301 Moved Permanantly\n";

if ( $CGI->param('act') eq 'SC' )
{
	$act	= 'forums';
	$id		= 'c' . $CGI->param('c');
	
	print "Location: $URL_TO_IPB/ikonboard.php?act=$act&id=$id\n\n";
	exit;
} elsif ( $CGI->param('act') eq 'SF' )
{
	$act	= 'forums';
	$id		= $CGI->param('f');
	
	print "Location: $URL_TO_IPB/ikonboard.php?act=$act&id=$id\n\n";
	exit;
} elsif ( $CGI->param('act') eq 'ST' )
{
	$act	= 'topics';
	$id		= $CGI->param('t');
	
	print "Location: $URL_TO_IPB/ikonboard.php?act=$act&id=$id\n\n";
	exit;
} elsif ( $CGI->param('act') eq 'Profile' )
{
	$act	= 'profile';
	$id		= $CGI->param('MID'); # A timestamp may be included - we'll use PHP to strip it.
	
	print "Location: $URL_TO_IPB/ikonboard.php?act=$act&id=$id\n\n";
	exit;
} elsif ( $CGI->param('act') eq 'Members' )
{
	$act	= 'members';
	
	print "Location: $URL_TO_IPB/ikonboard.php?act=$act\n\n";
	exit;
} else {
	print "Location: $URL_TO_IPB\n\n";
	exit;
}