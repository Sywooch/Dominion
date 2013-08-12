var _XDOC;
var ECMAP=new Array;

ECMAP['cs49']="command('q',' n=\"1\"')";
ECMAP['cs50']="command('q',' n=\"2\"')";
ECMAP['cs51']="command('q',' n=\"3\"')";
ECMAP['cs52']="command('q',' n=\"4\"')";
ECMAP['cs53']="command('q',' n=\"5\"')";
ECMAP['cs54']="command('q',' n=\"6\"')";
ECMAP['cs55']="command('q',' n=\"7\"')";
ECMAP['cs56']="command('q',' n=\"8\"')";
ECMAP['cs57']="command('q',' n=\"9\"')";
ECMAP['cs187']="command('sup','')";
ECMAP['cs83']="command('story','')";
ECMAP['cs66']='command("b","")';
ECMAP['cs73']='command("i","")';
ECMAP['cs80']='command("p","")';
ECMAP['cs82']='command("preface","")';
ECMAP['cs69']='command("extra","")';
ECMAP['cs86']="command('picture',' zoom=\"yes\" format=\"jpg\"')";
ECMAP['cs81']='command("headline","")';
ECMAP['cs13']="commandone('br')";
ECMAP['cs88']='command("gbox","")';
ECMAP['cs189']='command("sub","")';
ECMAP['cs89']="command('symbol',' value=\"\"')";
ECMAP['cs32']="commandone('nbsp')";
ECMAP['c49']='command("nobr","")';
ECMAP['cs90']='command("box","")';
ECMAP['cs74']="command('qq',' id=\"dp\"')";
ECMAP['cs65']="command('box',' width=\"full\"')";
ECMAP['cs70']="command('preface','')";
ECMAP['cs67']="command('symbol',' value=\"copy\"')";
ECMAP['cs76']="command('li','')";
ECMAP['cs87']="command('title','')";

function commandSet(tag,attrs,val)
{
 if(_XDOC)
 {
  _XDOC.focus();
  if(document.all)
  {
  var tr=document.selection.createRange();
  tr.text='<'+tag+attrs+'>'+val+'</'+tag+'>';
  tr.select();
  }
  else
  {
	var oldtop=_XDOC.scrollTop;
	var start = _XDOC.selectionStart;
	var end   = _XDOC.selectionEnd;
	var sel1  = _XDOC.value.substr(0,start);
	var sel2  = _XDOC.value.substr(end);
	var sel   = '<'+tag+attrs+'>'+val+'</'+tag+'>';
	_XDOC.value =sel1+sel+sel2;
	_XDOC.setSelectionRange(start,start+sel.length);
	_XDOC.scrollTop=oldtop;
  }
 }
return false;
}

function command(tag,attrs)
{
 if(_XDOC)
 {
  _XDOC.focus();
  if(document.all)
  {
  var tr=document.selection.createRange();
  tr.text='<'+tag+attrs+'>'+tr.text+'</'+tag+'>';
  tr.select();
  }
  else
  {
    var oldtop=_XDOC.scrollTop;
    var start = _XDOC.selectionStart;
    var end   = _XDOC.selectionEnd;
    var sel1  = _XDOC.value.substr(0,start);
    var sel2  = _XDOC.value.substr(end);
    var sel   = '<'+tag+attrs+'>'+_XDOC.value.substr(start,end - start)+'</'+tag+'>';
    _XDOC.value =sel1+sel+sel2;
    _XDOC.setSelectionRange(start,start+sel.length);
    _XDOC.scrollTop=oldtop;
  }
 }
return false;
}

function commandone(tag)
{
 if(_XDOC)
 {
  if(document.all)
  {
	_XDOC.focus();
	var tr=document.selection.createRange();
	tr.text='<'+tag+'/>';
	tr.select();
  }
  else
  {
	var oldtop=_XDOC.scrollTop;
	var start = _XDOC.selectionStart;
	var end   = _XDOC.selectionEnd;
	var sel1  = _XDOC.value.substr(0,start);
	var sel2  = _XDOC.value.substr(end);
	_XDOC.value =sel1+'<'+tag+"/>\n"+sel2;
	var pos=start+tag.length+4;
	_XDOC.setSelectionRange(pos,pos);
	_XDOC.scrollTop=oldtop;
  }
 }
return false;
}

function tabler()
{
var win=window.open('', 'modal','directories=no,titlebar=no,menubar=no,resizable=no,scrollbars=no,status=no,toolbar=no,top =10,width=300,height=300,left=10', false);
win.document.open();
win.document.write("<body bgcolor=#a0a0a0 text=white><b><form onsubmit='opener.ins_table(rows.value,cols.value,border.value,transparent.value,align.value);window.close();return false;'>Строк:<br><input type=text id=rows size=10><br>Колонок:<br><input type=text id=cols size=10><br>Рамка:<br><select id=border><option value='1'>Есть</option><option value='0'>Нет</option></select><br>Прозрачность:<br><select id=transparent><option value='0'>Непрозрачная</option><option value='1'>Прозрачная</option></select><br>Pacположение:<br><select id=align><option value=''>По умолчанию</option><option value='left'>Влево</option><option value='right'>Вправо</option></select><br><input type=submit value='Создать'></form></b></body>");
win.document.close();
win.focus();
return false;
}

function picture()
{
var win=window.open('', 'modal','directories=no,titlebar=no,menubar=no,resizable=no,scrollbars=no,status=no,toolbar=no,top =10,width=300,height=300,left=10', false);
win.document.open();
win.document.write("<body bgcolor=#a0a0a0 text=white><b><form onsubmit='opener.ins_picture(name.value,zoom.value,format.value,align.value);window.close();return false;'>Название:<br><input type=text id=name size=10 style='width:100%'><br>Увеличивать:<br><select id=zoom><option value='yes'>Да</option><option value='no'>Нет</option></select><br>Формат:<br><select id=format><option value='jpg'>jpg</option><option value='gif'>gif</option><option value='png'>png</option></select><br>Pacположение:<br><select id=align><option value=''>По умолчанию</option><option value='left'>Влево</option><option value='right'>Вправо</option></select><br><br><input type=submit value='Создать'></form></b></body>");
win.document.close();
win.focus();
return false;
}

function _link()
{
var win=window.open( '', 'modal','directories=no,titlebar=no,menubar=no,resizable=no,scrollbars=no,status=no,toolbar=no,top=10,width=350,height=150,left=10', false);
win.document.open();
win.document.write("<body bgcolor=#a0a0a0 text=white><b><form onsubmit='opener.ins_link(name.value,bnew.value);window.close();return false;'><u>URL</u>:<br><input type=text id=name size=45 style='width:100%'><br>В новом окне<br><select name='bnew'><option value='0'>--Нет--</option><option value='1'>Да</option></select><br><br><input type=submit value='Создать'></form></b></body>");
win.document.close();
win.focus();
return false;
}

function s_ancor()
{
var win=window.open( '', 'modal','directories=no,titlebar=no,menubar=no,resizable=no,scrollbars=no,status=no,toolbar=no,top=10,width=350,height=150,left=10', false);
win.document.open();
win.document.write("<body bgcolor=#a0a0a0 text=white><b><form onsubmit='opener.ins_ancor(name.value);window.close();return false;'><u>Название</u>:<input type=text id=name size=45 style='width:100%'><br><br><input type=submit value='Создать'></form></b></body>");
win.document.close();
win.focus();
return false;
}

function _style()
{
var win=window.open( 'style.plx', 'modal','directories=no,titlebar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no,top=10,width=550,height=250,left=10', false);
win.focus();
return false;
}

function ins_style(name,type)
{
command("span",' class="'+name+'"');
return false;
}

function ins_ol_ul(name)
{
if(_XDOC)
{
if(document.all)
 {
	_XDOC.focus();
	var tr=document.selection.createRange();
	var tmp,i;
	var txt=tr.text;
	txt=txt.replace(/\r/g,"");
	var rows=txt.split("\n");
	tmp='';
	for (i=0;i<rows.length;i++)
	{
	 tmp=tmp+"<li>"+rows[i]+"</li>\n";
	}
	tr.text='<'+name+">\n"+tmp+'</'+name+'>';
	tr.select();
 }
else
 {
	var tmp,i;
	var oldtop=_XDOC.scrollTop;
	var start = _XDOC.selectionStart;
	var end   = _XDOC.selectionEnd;
	var sel1  = _XDOC.value.substr(0,start);
	var sel2  = _XDOC.value.substr(end);
	var txt   = _XDOC.value.substr(start,end - start);
	txt=txt.replace(/\r/g,"");
	var rows=txt.split("\n");
	tmp='';
	for (i=0;i<rows.length;i++)
	{
		tmp=tmp+"<li>"+rows[i]+"</li>\n";
	}
	_XDOC.value=sel1+"<"+name+">\n"+tmp+'</'+name+'>'+sel2;
	_XDOC.setSelectionRange(start,start+tmp.length+10);
	_XDOC.scrollTop=oldtop;
 }
}
return false;
}

function ins_table(rows,cols,border,transparent,align)
{
if(_XDOC)
{
var tmp,i,j;
tmp="\n";
for (i=0;i<rows;i++)
{
 tmp=tmp+"<tr>";
   for (j=0;j<cols;j++)
   {
    tmp=tmp+"<td></td>";
   }
 tmp=tmp+"</tr>\n";
}
if(transparent >0)transparent=' transparent="'+transparent+'"'; else  transparent='';
if(border >0)border=' border="'+border+'"'; else border='';
if(align !='' )align=' align="'+align+'"'; else align='';
commandSet('table'," "+border+transparent+align,tmp);
}
}

function ins_picture(name,zoom,format,align)
{
command('picture',' name="'+name+'" zoom="'+zoom+'" format="'+format+'" align="'+align+'"');
}

function ins_ancor(name)
{
commandone('ancor name="'+name+'"');
}

function ins_link(name,bnew)
{
if(bnew=='1') bnew=' target="_blank"'; else bnew='';
command('link',' href="'+name+'"'+bnew);
}

function _etaKey(ev) {
if(document.all) ev=window.event;
if(!(ev.ctrlKey || ev.shiftKey || ev.altKey)) return false;
	var ka="";
	if(ev.ctrlKey) ka+="c";
	if(ev.shiftKey)ka+="s";
	if(ev.altKey)ka+="a";
	ka+=ev.keyCode;
//	window.status=ka;
	var mac=ECMAP[ka];
	if(mac){
		if(mac.charAt(0)=="^") 
			command(mac.substr(1));
		else eval(mac);
		if(document.all)
		{
		 ev.keyCode=0;
		 ev.returnValue=false;
  		}
		else
		{
		 ev.cancelBubble = true;
		 ev.preventDefault();
		 ev.stopPropagation();
		}
		return false;
	}
}