<?php
/**
 * Nette Framework
 *
 * Copyright (c) 2004, 2008 David Grudl (http://davidgrudl.com)
 *
 * This source file is subject to the "Nette license" that is bundled
 * with this package in the file license.txt.
 *
 * For more information please see http://nettephp.com
 *
 * @copyright  Copyright (c) 2004, 2008 David Grudl
 * @license    http://nettephp.com/license  Nette license
 * @link       http://nettephp.com
 * @category   Nette
 * @package    Nette
 * @version    $Id$
 */

class
ArgumentOutOfRangeException
extends
InvalidArgumentException{}class
InvalidStateException
extends
RuntimeException{}class
NotImplementedException
extends
LogicException{}class
NotSupportedException
extends
LogicException{}class
MemberAccessException
extends
LogicException{}class
IOException
extends
RuntimeException{}class
FileNotFoundException
extends
IOException{}class
DirectoryNotFoundException
extends
IOException{}class
FatalErrorException
extends
Exception{public
function
__construct($message,$code,$severity,$file,$line,$context){parent::__construct($message,$code);$this->file=$file;$this->line=$line;$this->context=$context;}}final
class
Framework{const
VERSION='0.8';const
REVISION='106 released on 2008/10/27 11:13:57';final
public
function
__construct(){throw
new
LogicException("Cannot instantiate static class ".get_class($this));}public
static
function
compareVersion($version){return
version_compare($version,self::VERSION);}public
static
function
promo($xhtml=TRUE){echo'<a href="http://nettephp.com/" title="Nette Framework - The Most Innovative PHP Framework"><img ','src="http://nettephp.com/images/nette-powered.gif" alt="Powered by Nette Framework" width="80" height="15"',($xhtml?' />':'>'),'</a>';}}final
class
Debug{public
static$counters=array();public
static$html;public
static$maxDepth=3;public
static$maxLen=150;private
static$enabled=FALSE;public
static$useFirebug;private
static$logFile;private
static$logHandle;private
static$sendEmails;private
static$emailHeaders=array('To'=>'','From'=>'noreply@%host%','X-Mailer'=>'Nette Framework','Subject'=>'PHP: An error occurred on the server %host%','Body'=>'[%date%]');public
static$mailer=array(__CLASS__,'sendEmail');public
static$emailProbability=0.01;public
static$keysToHide=array('password','passwd','pass','pwd','creditcard','credit card','cc','pin');private
static$colophons=array(array(__CLASS__,'getDefaultColophons'));private
static$keyFilter=array();public
static$time;private
static$fireCounter;const
LOG='LOG';const
INFO='INFO';const
WARN='WARN';const
ERROR='ERROR';final
public
function
__construct(){throw
new
LogicException("Cannot instantiate static class ".get_class($this));}public
static
function
dump($var,$return=FALSE){self::$keyFilter=FALSE;$output="<pre class=\"dump\">".self::_dump($var,0)."</pre>\n";if(!self::$html){$output=htmlspecialchars_decode(strip_tags($output),ENT_NOQUOTES);}if($return){return$output;}else{echo$output;return$var;}}private
static
function
_dump(&$var,$level){if(is_bool($var)){return"<span>bool</span>(".($var?'TRUE':'FALSE').")\n";}elseif($var===NULL){return"<span>NULL</span>\n";}elseif(is_int($var)){return"<span>int</span>($var)\n";}elseif(is_float($var)){return"<span>float</span>($var)\n";}elseif(is_string($var)){if(self::$maxLen&&strlen($var)>self::$maxLen){$s=htmlSpecialChars(substr($var,0,self::$maxLen),ENT_NOQUOTES).' ... ';}else{$s=htmlSpecialChars($var,ENT_NOQUOTES);}return"<span>string</span>(".strlen($var).") \"$s\"\n";}elseif(is_array($var)){$s="<span>array</span>(".count($var).") {\n";$space=str_repeat('  ',$level);static$marker;if($marker===NULL)$marker=uniqid("\x00",TRUE);if(isset($var[$marker])){$s.="$space  *RECURSION*\n";}elseif($level<self::$maxDepth||!self::$maxDepth){$var[$marker]=0;foreach($var
as$k=>&$v){if($k===$marker)continue;$s.="$space  ".(is_int($k)?$k:"\"$k\"")." => ";if(self::$keyFilter&&is_string($v)&&isset(self::$keyFilter[strtolower($k)])){$s.="<span>string</span>(?) <i>*** hidden ***</i>\n";}else{$s.=self::_dump($v,$level+1);}}unset($var[$marker]);}else{$s.="$space  ...\n";}return$s."$space}\n";}elseif(is_object($var)){$arr=(array)$var;$s="<span>object</span>(".get_class($var).") (".count($arr).") {\n";$space=str_repeat('  ',$level);static$list=array();if(in_array($var,$list,TRUE)){$s.="$space  *RECURSION*\n";}elseif($level<self::$maxDepth||!self::$maxDepth){$list[]=$var;foreach($arr
as$k=>&$v){$m='';if($k[0]==="\x00"){$m=$k[1]==='*'?' <span>protected</span>':' <span>private</span>';$k=substr($k,strrpos($k,"\x00")+1);}$s.="$space  \"$k\"$m => ";if(self::$keyFilter&&is_string($v)&&isset(self::$keyFilter[strtolower($k)])){$s.="<span>string</span>(?) <i>*** hidden ***</i>\n";}else{$s.=self::_dump($v,$level+1);}}array_pop($list);}else{$s.="$space  ...\n";}return$s."$space}\n";}elseif(is_resource($var)){return"<span>resource of type</span>(".get_resource_type($var).")\n";}else{return"<span>unknown type</span>\n";}}public
static
function
timer(){static$time=0;$now=microtime(TRUE);$delta=$now-$time;$time=$now;return$delta;}public
static
function
enable($level=E_ALL,$logErrors=NULL,$sendEmails=FALSE){if(version_compare(PHP_VERSION,'5.2.1')===0){throw
new
NotSupportedException(__METHOD__.' is not supported in PHP 5.2.1');}if($logErrors===NULL&&class_exists('Environment')){$logErrors=Environment::isLive();}if(self::$useFirebug===NULL){self::$useFirebug=function_exists('json_encode')&&!$logErrors&&isset($_SERVER['HTTP_USER_AGENT'])&&strpos($_SERVER['HTTP_USER_AGENT'],'FirePHP/');}if($level!==NULL){error_reporting($level);}if(function_exists('ini_set')){ini_set('display_startup_errors',!$logErrors);ini_set('display_errors',!$logErrors);ini_set('html_errors',self::$html);ini_set('log_errors',(bool)$logErrors);}elseif($logErrors){throw
new
NotSupportedException('Function ini_set() is not enabled.');}if($logErrors){if(is_string($logErrors)){self::$logFile=strpos($logErrors,'%')===FALSE?$logErrors:Environment::expand($logErrors);}else{try{self::$logFile=Environment::expand('%logDir%/php_error.log');}catch(InvalidStateException$e){self::$logFile='php_error.log';}}ini_set('error_log',self::$logFile);}self::$sendEmails=$logErrors&&$sendEmails;if(self::$sendEmails){if(is_string($sendEmails)){self::$emailHeaders['To']=$sendEmails;}elseif(is_array($sendEmails)){self::$emailHeaders=$sendEmails+self::$emailHeaders;}if(mt_rand()/mt_getrandmax()<self::$emailProbability){self::observeErrorLog();}}if(!defined('E_RECOVERABLE_ERROR')){define('E_RECOVERABLE_ERROR',4096);}if(!defined('E_DEPRECATED')){define('E_DEPRECATED',8192);}set_exception_handler(array(__CLASS__,'exceptionHandler'));set_error_handler(array(__CLASS__,'errorHandler'));self::$enabled=TRUE;}public
static
function
isEnabled(){return
self::$enabled;}public
static
function
exceptionHandler(Exception$exception){if(!headers_sent()){header('HTTP/1.1 500 Internal Server Error');}if(self::$logFile){error_log("PHP Fatal error:  Uncaught $exception");$file=@strftime('%d-%b-%Y %H-%M-%S ',Debug::$time).strstr(number_format(Debug::$time,4,'~',''),'~');$file=dirname(self::$logFile)."/exception $file.html";self::$logHandle=@fopen($file,'x');if(self::$logHandle){ob_start(array(__CLASS__,'writeFile'),1);self::paintBlueScreen($exception);ob_end_flush();fclose(self::$logHandle);}self::observeErrorLog();}elseif(!self::$html||isset($_SERVER['HTTP_X_REQUESTED_WITH'])&&$_SERVER['HTTP_X_REQUESTED_WITH']==='XMLHttpRequest'){if(self::$useFirebug&&!headers_sent()){self::fireLog($exception);}else{echo"$exception\n";foreach(self::$colophons
as$callback){foreach((array)call_user_func($callback,'bluescreen')as$line)echo
strip_tags($line)."\n";}}}else{while(ob_get_level()&&@ob_end_clean());self::paintBlueScreen($exception);exit;}}public
static
function
errorHandler($severity,$message,$file,$line,$context){static$fatals=array(E_ERROR=>1,E_CORE_ERROR=>1,E_COMPILE_ERROR=>1,E_USER_ERROR=>1,E_PARSE=>1,E_RECOVERABLE_ERROR=>1);if(isset($fatals[$severity])){throw
new
FatalErrorException($message,0,$severity,$file,$line,$context);}elseif(($severity&error_reporting())!==$severity){return
NULL;}elseif(self::$useFirebug&&!headers_sent()){$types=array(E_WARNING=>'Warning',E_USER_WARNING=>'Warning',E_NOTICE=>'Notice',E_USER_NOTICE=>'Notice',E_STRICT=>'Strict standards',E_DEPRECATED=>'Deprecated');$type=isset($types[$severity])?$types[$severity]:'Unknown error';$message=strip_tags($message);self::fireLog("$type: $message in $file on line $line",'WARN');return
NULL;}return
FALSE;}public
static
function
paintBlueScreen(Exception$exception){$colophons=self::$colophons;function
_netteDebugPrintCode($file,$line,$count=15){if(function_exists('ini_set')){ini_set('highlight.comment','#999; font-style: italic');ini_set('highlight.default','#000');ini_set('highlight.html','#06b');ini_set('highlight.keyword','#d24; font-weight: bold');ini_set('highlight.string','#080');}$start=max(1,$line-floor($count/2));$source=explode("\n",@highlight_file($file,TRUE));echo$source[0];$source=explode('<br />',$source[1]);array_unshift($source,NULL);$i=$start;while(--$i>=1){if(preg_match('#.*(</?span[^>]*>)#',$source[$i],$m)){if($m[1]!=='</span>')echo$m[1];break;}}$source=array_slice($source,$start,$count,TRUE);end($source);$numWidth=strlen((string)key($source));foreach($source
as$n=>$s){$s=str_replace(array("\r","\n"),array('',''),$s);if($n===$line){printf("<span class='highlight'>Line %{$numWidth}s:    %s\n</span>%s",$n,strip_tags($s),preg_replace('#[^>]*(<[^>]+>)[^<]*#','$1',$s));}else{printf("<span class='line'>Line %{$numWidth}s:</span>    %s\n",$n,$s);}}echo'</span></span></code>';}function
_netteOpenPanel($name,$collaped){static$id;$id++;?>
	<div class="panel">
		<h2><a href="#" onclick="return !toggle(this, 'pnl<?php echo$id?>')"><?php echo
htmlSpecialChars($name)?> <span><?php echo$collaped?'&#x25b6;':'&#x25bc;'?></span></a></h2>

		<div id="pnl<?php echo$id?>" class="<?php echo$collaped?'collapsed ':''?>inner">
	<?php
}function
_netteClosePanel(){?>
		</div>
	</div>
	<?php
}if(headers_sent()){echo'</pre></xmp></table>';}?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="robots" content="noindex,noarchive">
	<meta name="generator" content="Nette Framework">

	<title><?php echo
htmlspecialchars(get_class($exception))?></title>

	<style type="text/css">
	/* <![CDATA[ */
		body {
			font: 78%/1.5 Verdana, sans-serif;
			background: white;
			color: #333;
			margin: 0 0 2em;
			padding: 0;
		}

		h1 {
			font-weight: normal !important;
			font-size: 18pt;
			margin: .6em 0;
		}

		h2 {
			font-family: sans-serif;
			font-weight: normal;
			font-size: 14pt;
			color: #888;
			margin: .6em 0;
		}

		a {
			text-decoration: none;
			color: #4197E3;
		}

		a span {
			color: #999;
		}

		h3 {
			font-size: 110%;
			font-weight: bold;
			margin: 1em 0;
		}

		p { margin: .8em 0 }

		pre, code, table {
			font-family: Consolas, monospace;
		}

		pre, table {
			background: #ffffcc;
			padding: .4em .7em;
			border: 1px dotted silver;
		}

		table pre {
			padding: 0;
			margin: 0;
			border: none;
			font-size: 100%;
		}

		pre.dump span {
			color: #c16549;
		}

		div.panel {
			border-bottom: 1px solid #eee;
			padding: 1px 2em;
		}

		div.inner {
			padding: 0.1em 1em 1em;
			background: #f5f5f5;
		}

		table {
			border-collapse: collapse;
			width: 100%;
		}

		td, th {
			vertical-align: top;
			padding: 2px 3px;
			border: 1px solid #eeeebb;
		}

		ul {
			font-size: 80%;
		}

		.highlight, #error {
			background: red;
			color: white;
			font-weight: bold;
			font-style: normal;
			display: block;
		}

		.line {
			color: #9e9e7e;
			font-weight: normal;
			font-style: normal;
		}

	/* ]]> */
	</style>


	<script type="text/javascript">
	/* <![CDATA[ */
		document.write('<style> .collapsed { display: none; } </style>');

		function toggle(link, panel)
		{
			var span = link.getElementsByTagName('span')[0];
			var div = document.getElementById(panel);
			var collapsed = div.currentStyle ? div.currentStyle.display == 'none' : getComputedStyle(div, null).display == 'none';

			span.innerHTML = String.fromCharCode(collapsed ? 0x25bc : 0x25b6);
			div.style.display = collapsed ? 'block' : 'none';

			return true;
		}
	/* ]]> */
	</script>
</head>



<body>
	<div id="error" class="panel">
		<h1><?php echo
htmlspecialchars(get_class($exception)),($exception->getCode()?' #'.$exception->getCode():'')?></h1>

		<p><?php echo
htmlspecialchars($exception->getMessage())?></p>
	</div>



	<?php $ex=$exception;$level=0;?>
	<?php do{?>

		<?php if($level++):?>
			<?php _netteOpenPanel('Caused by',TRUE)?>
			<div class="panel">
				<h1><?php echo
htmlspecialchars(get_class($ex)),($ex->getCode()?' #'.$ex->getCode():'')?></h1>

				<p><?php echo
htmlspecialchars($ex->getMessage())?></p>
			</div>
		<?php endif?>

		<?php if(is_file($ex->getFile())):?>
		<?php _netteOpenPanel('Source file',FALSE)?>
			<p><strong>File:</strong> <?php echo
htmlspecialchars($ex->getFile())?> &nbsp; <strong>Line:</strong> <?php echo$ex->getLine()?></p>
			<pre><?php _netteDebugPrintCode($ex->getFile(),$ex->getLine())?></pre>
		<?php _netteClosePanel()?>
		<?php endif?>



		<?php _netteOpenPanel('Call stack',FALSE)?>
			<ol>
				<?php foreach($ex->getTrace()as$key=>$row):?>
				<li><p>

				<?php if(isset($row['file'])):?>
					<span title="<?php echo
htmlSpecialChars($row['file'])?>"><?php echo
htmlSpecialChars(basename(dirname($row['file']))),'/<b>',htmlSpecialChars(basename($row['file'])),'</b></span> (',$row['line'],')'?>
				<?php else:?>
					&lt;PHP inner-code&gt;
				<?php endif?>

				<?php if(isset($row['file'])&&is_file($row['file'])):?><a href="#" onclick="return !toggle(this, 'src<?php echo"$level-$key"?>')">source <span>&#x25b6;</span></a> &nbsp; <?php endif?>

				<?php if(isset($row['class']))echo$row['class'].$row['type']?>
				<?php echo$row['function']?>

				(<?php if(!empty($row['args'])):?><a href="#" onclick="return !toggle(this, 'args<?php echo"$level-$key"?>')">arguments <span>&#x25b6;</span></a><?php endif?>)
				</p>

				<?php if(!empty($row['args'])):?>
					<div class="collapsed" id="args<?php echo"$level-$key"?>">
					<table>
					<?php

try{$r=isset($row['class'])?new
ReflectionMethod($row['class'],$row['function']):new
ReflectionFunction($row['function']);$params=$r->getParameters();}catch(Exception$e){$params=array();}foreach($row['args']as$k=>$v){echo'<tr><td>',(isset($params[$k])?'$'.$params[$k]->name:"#$k"),'</td>';echo'<td>',self::safeDump($v,isset($params[$k])?$params[$k]->name:NULL),"</td></tr>\n";}?>
					</table>
					</div>
				<?php endif?>


				<?php if(isset($row['file'])&&is_file($row['file'])):?>
					<pre class="collapsed" id="src<?php echo"$level-$key"?>"><?php _netteDebugPrintCode($row['file'],$row['line'])?></pre>
				<?php endif?>

				</li>
				<?php endforeach?>
			</ol>
		<?php _netteClosePanel()?>



		<?php if($ex
instanceof
IDebuggable):?>
		<?php foreach($ex->getPanels()as$name=>$panel):?>
		<?php _netteOpenPanel($name,empty($panel['expanded']))?>
			<?php echo$panel['content']?>
		<?php _netteClosePanel()?>
		<?php endforeach?>
		<?php endif?>



		<?php if(isset($ex->context)&&is_array($ex->context)):?>
		<?php _netteOpenPanel('Variables',TRUE)?>
		<table>
		<?php

foreach($ex->context
as$k=>$v){echo'<tr><td>$',htmlspecialchars($k),'</td><td>',self::safeDump($v,$k),"</td></tr>\n";}?>
		</table>
		<?php _netteClosePanel()?>
		<?php endif?>

	<?php }while((method_exists($ex,'getPrevious')&&$ex=$ex->getPrevious())||(isset($ex->previous)&&$ex=$ex->previous));?>
	<?php while(--$level)_netteClosePanel()?>





	<?php _netteOpenPanel('Environment',TRUE)?>
		<?php
$list=get_defined_constants(TRUE);if(!empty($list['user'])):?>
		<h3><a href="#" onclick="return !toggle(this, 'pnl-env-const')">Constants <span>&#x25bc;</span></a></h3>
		<table id="pnl-env-const">
		<?php

foreach($list['user']as$k=>$v){echo'<tr><td>',htmlspecialchars($k),'</td><td>',self::safeDump($v,$k),"</td></tr>\n";}?>
		</table>
		<?php endif?>


		<h3><a href="#" onclick="return !toggle(this, 'pnl-env-files')">Included files <span>&#x25b6;</span></a> (<?php echo
count(get_included_files())?>)</h3>
		<table id="pnl-env-files" class="collapsed">
		<?php

foreach(get_included_files()as$v){echo'<tr><td>',htmlspecialchars($v),"</td></tr>\n";}?>
		</table>


		<h3>$_SERVER</h3>
		<?php if(empty($_SERVER)):?>
		<p><i>empty</i></p>
		<?php else:?>
		<table>
		<?php

foreach($_SERVER
as$k=>$v)echo'<tr><td>',htmlspecialchars($k),'</td><td>',self::dump($v,TRUE),"</td></tr>\n";?>
		</table>
		<?php endif?>
	<?php _netteClosePanel()?>




	<?php _netteOpenPanel('HTTP request',TRUE)?>
		<?php if(function_exists('apache_request_headers')):?>
		<h3>Headers</h3>
		<table>
		<?php

foreach(apache_request_headers()as$k=>$v)echo'<tr><td>',htmlspecialchars($k),'</td><td>',htmlspecialchars($v),"</td></tr>\n";?>
		</table>
		<?php endif?>


		<?php foreach(array('_GET','_POST','_COOKIE')as$name):?>
		<h3>$<?php echo$name?></h3>
		<?php if(empty($GLOBALS[$name])):?>
		<p><i>empty</i></p>
		<?php else:?>
		<table>
		<?php

foreach($GLOBALS[$name]as$k=>$v)echo'<tr><td>',htmlspecialchars($k),'</td><td>',self::dump($v,TRUE),"</td></tr>\n";?>
		</table>
		<?php endif?>
		<?php endforeach?>
	<?php _netteClosePanel()?>



	<?php _netteOpenPanel('HTTP response',TRUE)?>
		<h3>Headers</h3>
		<?php if(headers_list()):?>
		<pre><?php

foreach(headers_list()as$s)echo
htmlspecialchars($s),'<br>';?></pre>
		<?php else:?>
		<p><i>no headers</i></p>
		<?php endif?>
	<?php _netteClosePanel()?>


	<ul>
		<?php foreach($colophons
as$callback):?>
		<?php foreach((array)call_user_func($callback,'bluescreen')as$line):?><li><?php echo$line,"\n"?></li><?php endforeach?>
		<?php endforeach?>
	</ul>

</body>
</html><?php }public
static
function
writeFile($buffer){fwrite(self::$logHandle,$buffer);}private
static
function
observeErrorLog(){if(!self::$sendEmails)return;$monitorFile=self::$logFile.'.monitor';$saved=@file_get_contents($monitorFile);$actual=(int)@filemtime(self::$logFile);if($saved===FALSE){file_put_contents($monitorFile,$actual);}elseif(is_numeric($saved)&&$saved!=$actual){if(file_put_contents($monitorFile,'e-mail has been sent')){call_user_func(self::$mailer);}}}private
static
function
sendEmail(){$host=isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:(isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:'');$headers=str_replace(array('%host%','%date%'),array($host,@date('Y-m-d H:i:s',Debug::$time)),self::$emailHeaders);$subject=$headers['Subject'];$to=$headers['To'];$body=$headers['Body'];unset($headers['Subject'],$headers['To'],$headers['Body']);$header='';foreach($headers
as$key=>$value){$header.="$key: $value\r\n";}$body=str_replace("\r\n","\n",$body);if(PHP_OS!='Linux')$body=str_replace("\n","\r\n",$body);if($to==='debug'){self::dump(array($to,$subject,$body,$header));}else{mail($to,$subject,$body,$header);}}private
static
function
safeDump($var,$key=NULL){self::$keyFilter=array_change_key_case(array_flip(self::$keysToHide),CASE_LOWER);if($key!==NULL&&isset(self::$keyFilter[strtolower($key)])){return'<i>*** hidden ***</i>';}return"<pre class=\"dump\">".self::_dump($var,0)."</pre>\n";}public
static
function
enableProfiler(){register_shutdown_function(array(__CLASS__,'paintProfiler'));}public
static
function
paintProfiler(){$colophons=self::$colophons;if(self::$useFirebug){foreach(self::$colophons
as$callback){foreach((array)call_user_func($callback,'profiler')as$line)self::fireLog(strip_tags($line));}}if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])||$_SERVER['HTTP_X_REQUESTED_WITH']!=='XMLHttpRequest'){?>
</pre></xmp>

<style type="text/css">
/* <![CDATA[ */
	#netteProfilerContainer {
		position: absolute;
		right: 5px;
		bottom: 5px;
	}

	#netteProfiler {
		position: relative;
		margin: 0;
		padding: 1px;
		width: 350px;
		color: black;
		background: #EEE;
		border: 1px dotted gray;
		cursor: move;
		opacity: .70;
		=filter: alpha(opacity=70);
	}

	#netteProfiler:hover {
		opacity: 1;
		=filter: none;
	}

	#netteProfiler li {
		margin: 0;
		padding: 1px;
		font: normal normal 11px/1.4 Consolas, Arial;
		text-align: left;
		list-style: none;
	}

	#netteProfiler span[title] {
		border-bottom: 1px dotted gray;
		cursor: help;
	}

	#netteProfiler strong {
		color: red;
	}
/* ]]> */
</style>


<div id="netteProfilerContainer">
<ul id="netteProfiler">
	<?php foreach($colophons
as$callback):?>
	<?php foreach((array)call_user_func($callback,'profiler')as$line):?><li><?php echo$line,"\n"?></li><?php endforeach?>
	<?php endforeach?>
</ul>
</div>


<script type="text/javascript">
/* <![CDATA[ */
document.getElementById('netteProfiler').onmousedown = function(e) {
	e = e || event;
	this.posX = parseInt(this.style.left + '0');
	this.posY = parseInt(this.style.top + '0');
	this.mouseX = e.clientX;
	this.mouseY = e.clientY;

	var thisObj = this;

	document.documentElement.onmousemove = function(e) {
		e = e || event;
		thisObj.style.left = (e.clientX - thisObj.mouseX + thisObj.posX) + "px";
		thisObj.style.top = (e.clientY - thisObj.mouseY + thisObj.posY) + "px";
		return false;
	};

	document.documentElement.onmouseup = function(e) {
		document.documentElement.onmousemove = null;
		document.documentElement.onmouseup = null;
		return false;
	};
};
/* ]]> */
</script>
<?php }}public
static
function
addColophon($callback){if(!in_array($callback,self::$colophons,TRUE)&&is_callable($callback)){self::$colophons[]=$callback;}}public
static
function
getDefaultColophons($sender){if($sender==='profiler'){$arr[]='Elapsed time: '.sprintf('%0.3f',(microtime(TRUE)-Debug::$time)*1000).' ms';foreach((array)self::$counters
as$name=>$value){if(is_array($value))$value=implode(', ',$value);$arr[]=htmlSpecialChars($name).' = <strong>'.htmlSpecialChars($value).'</strong>';}$autoloaded=class_exists('AutoLoader',FALSE)?AutoLoader::$count:0;$s='<span>'.count(get_included_files()).'/'.$autoloaded.' files</span>, ';$exclude=array('stdClass','Exception','ErrorException','Traversable','IteratorAggregate','Iterator','ArrayAccess','Serializable','Closure');foreach(get_loaded_extensions()as$ext){$ref=new
ReflectionExtension($ext);$exclude=array_merge($exclude,$ref->getClassNames());}$classes=array_diff(get_declared_classes(),$exclude);$intf=array_diff(get_declared_interfaces(),$exclude);$func=get_defined_functions();$func=(array)@$func['user'];$consts=get_defined_constants(TRUE);$consts=array_keys((array)@$consts['user']);foreach(array('classes','intf','func','consts')as$item){$s.='<span '.($$item?'title="'.implode(", ",$$item).'"':'').'>'.count($$item).' '.$item.'</span>, ';}$arr[]=$s;}if($sender==='bluescreen'){$arr[]='PHP '.PHP_VERSION;if(isset($_SERVER['SERVER_SOFTWARE']))$arr[]=htmlSpecialChars($_SERVER['SERVER_SOFTWARE']);$arr[]='Nette Framework '.Framework::VERSION.' (revision '.Framework::REVISION.')';$arr[]='Report generated at '.@strftime('%c',Debug::$time);}return$arr;}public
static
function
fireDump($var,$key){return
self::fireSend(2,array((string)$key=>$var));}public
static
function
fireLog($message,$priority=self::LOG,$label=NULL){if($message
instanceof
Exception){$priority='TRACE';$message=array('Class'=>get_class($message),'Message'=>$message->getMessage(),'File'=>$message->getFile(),'Line'=>$message->getLine(),'Trace'=>self::replaceObjects($message->getTrace()));}elseif($priority==='GROUP_START'){$label=$message;$message=NULL;}return
self::fireSend(1,array(array('Type'=>$priority,'Label'=>$label),$message));}private
static
function
fireSend($index,$payload){if(headers_sent())return
FALSE;if(!self::$fireCounter){header('X-Wf-Protocol-nette: http://meta.wildfirehq.org/Protocol/JsonStream/0.2');header('X-Wf-nette-Plugin-1: http://meta.firephp.org/Wildfire/Plugin/FirePHP/Library-FirePHPCore/0.2.0');header('X-Wf-nette-Structure-1: http://meta.firephp.org/Wildfire/Structure/FirePHP/FirebugConsole/0.1');header('X-Wf-nette-Structure-2: http://meta.firephp.org/Wildfire/Structure/FirePHP/Dump/0.1');}$payload=json_encode($payload);foreach(str_split($payload,4990)as$s){$num=++self::$fireCounter;header("X-Wf-nette-$index-1-$num: |$s|\\");}header("X-Wf-nette-$index-1-$num: |$s|");header("X-Wf-nette-Index: $num");return
TRUE;}static
private
function
replaceObjects($val){foreach($val
as$k=>$v){if(is_object($v)){$val[$k]='object '.get_class($v).'';}elseif(is_array($v)){$val[$k]=self::replaceObjects($v);}}return$val;}}Debug::$html=PHP_SAPI!=='cli';Debug::$time=microtime(TRUE);