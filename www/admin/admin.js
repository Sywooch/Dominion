function checkXML(obj)
{
    var tx=obj.value;
    var re,chr;
        re=new RegExp("\&amp\;","gi");
    tx=tx.replace(re,"<#amp#>");     
        re=new RegExp("\&\#([0-9]+)\;","gi");
    tx=tx.replace(re,"<#$1#>");     
        re=new RegExp("\&lt\;","gi");
    tx=tx.replace(re,"<#lt#>");     
        re=new RegExp("\&gt\;","gi");
    tx=tx.replace(re,"<#gt#>");     
        re=new RegExp("\&","gi");
    tx=tx.replace(re,"&amp;");  
        re=new RegExp("\<\#amp\#\>","gi");
    tx=tx.replace(re,"&amp;");   
        re=new RegExp("\<\#([0-9]+)\#\>","gi");
    tx=tx.replace(re,"&#$1;");   
        re=new RegExp("\<\#lt\#\>","gi");
    tx=tx.replace(re,"&lt;");   
        re=new RegExp("\<\#gt\#\>","gi");
    tx=tx.replace(re,"&gt;");   
    obj.value=tx;

if(document.all)
{
 var xd = new ActiveXObject('MSXML.DOMDocument');
 xd.async = false;
 xd.validateOnParse=true;
 var bOk=xd.loadXML("<A>"+tx+"</A>");
 var e=xd.parseError;
 if(!bOk){
  var fp=e.filepos-4;
  alert (e.reason);
   var tr=obj.createTextRange(); 
   tr.collapse(true);
   tr.moveStart("character",fp); tr.moveEnd("character",1);
   tr.select();
   obj.focus();
  return false;
 } else return true;
}
else
{
var parser = new DOMParser();
var doc = parser.parseFromString("<A>"+tx+"</A>", "text/xml");

var roottag = doc.documentElement;
if ((roottag.tagName == "parserError") ||
    (roottag.namespaceURI == "http://www.mozilla.org/newlayout/xml/parsererror.xml"))
{
  
  var error_text=roottag.firstChild.nodeValue;
  var error_subtext=roottag.firstChild.nextSibling.firstChild.nodeValue;
  var arr=/Line Number ([0-9]+), Column ([0-9]+)\:/.exec(error_text);
  var line=parseInt(arr[1]);
  var column=parseInt(arr[2]);
  alert(error_text +"\n-------------\n"+error_subtext);
  arr=tx.split('\n');
  var i,pos=0;
  for(i=0;i<(line-1);i++)pos+=arr[i].length;
  pos+=column+(line-1)-3;
  obj.setSelectionRange(pos,pos+error_subtext.length-3);
  _XDOC.scrollTop=pos;
  obj.focus();
  return false;
}
else return true;


}
 return true;
}

function checkEmail(obj){
    var str=obj.value;
    if(str=='') return true;
    if (/^([\w-~_]+\.)*[\w-~_]+@([\w-_]+\.){1,3}\w{2,4}$/.test(str))
        return true;
    else {
        alert("Неправильный e-mail адрес");
        obj.focus();
        return false;
    }
}

function IsDate(o)   {
   var dd=o.value;
if(dd=="") return true;
   var a=dd.split("-");
   if (a.length!=3 || a[2].length!=4 || a[1].length!=2 ||  a[0].length!=2 )
   {
     alert("Неверная дата");
     o.focus();
     return false;
   }
var ad=new Date(parseInt(a[2],10),parseInt(a[1],10)-1,parseInt(a[0],10));
if ((parseInt(a[0],10)==ad.getDate()) && ((parseInt(a[1],10)-1)==ad.getMonth()) && (parseInt(a[2],10)==ad.getFullYear()))   
{return true} 
else 
 {
     alert("Неверная дата");
     o.focus();
     return false
 }   
}

function SelectAll(f,mark,name)
{
  for (i = 0; i < f.elements.length; i++)
   {
    var item = f.elements[i];
    if (item.name == name)
     {
      item.checked = mark;
     };
   }
return true;
}

function dl()
{
return confirm('Подверждаете удаление?');
}

var ldr=null;

function add(sel,v,n){
var newOpt=sel.appendChild(document.createElement('option'));
newOpt.text=n;
newOpt.value=v;
}

function chan(f,name,qw,parm)
{
 if(ldr&&ldr.readyState!=0) { ldr.abort() }
 ldr=selector();

 if(ldr)
 {
        name.length = 0;
        var now = new Date();
//      alert("selector.plx?q="+parm+"&sel="+qw+"&t="+now.getSeconds());
        ldr.open("GET","selector.php?q="+parm+"&sel="+qw+"&t="+now.getSeconds(),true);
        //document.write("selector.php?q="+parm+"&sel="+qw+"&t="+now.getSeconds());
        ldr.onreadystatechange=function()
        {
        if(ldr.readyState==4 && ldr.responseText)
                {
                        //alert(ldr.responseText);
                        eval(ldr.responseText);
                }
        };
        ldr.send(null)
 }
}


function selector()
{
        var A=null;
        try{A=new ActiveXObject("Msxml2.XMLHTTP")}
        catch(e){try{A=new ActiveXObject("Microsoft.XMLHTTP")}
        catch(oc){A=null}}
        if(!A&&typeof XMLHttpRequest!="undefined") {A=new XMLHttpRequest()}
        return A
}


nodes= new Array ();
function clickOnFolder(fid)
{
   var obj,obj1;
   obj=document.getElementById("F_"+fid); obj1=document.getElementById("I_"+fid);
   if(obj)
   {
   if(nodes[fid])
   {
    nodes[fid]=false;
    obj.style.display="none";
    if(obj1)obj1.src="i/plus.gif"
   }
   else
   {
    nodes[fid]=true;
    obj.style.display="block";
    if(obj1)obj1.src="i/minus.gif"
   }
   } 
   return false;
}

function ch(obj)
{
        grandParent=obj.parentNode.parentNode;
        checkBox=grandParent.getElementsByTagName('input')[0];
        if(checkBox.name='id[]')
        {
                checkBox.checked=1;
                oldBackground=grandParent.style.backgroundColor;
                grandParent.style.backgroundColor="#FEF3B4";
                checkBox.onchange=function() {
                        newBackground=this.checked ? '#FEF3B4':oldBackground;
                        grandParent=this.parentNode.parentNode;
                        grandParent.style.background=newBackground;
                }
        }
        return false;
}

function cookieVal(cookieName) {
    thisCookie = document.cookie.split("; ")
        for (i = 0; i < thisCookie.length; i++) {
            if (cookieName == thisCookie[i].split("=")[0]) {
                return thisCookie[i].split("=")[1];
            }
        }
    return '';
}


function triggerSection(sectionId)
{
        section=document.getElementById('section_'+sectionId);
        oldState=section.style.display=='none' ? 'none' : 'block';
        if(oldState=='none')
        {
                newState='block';
                newIconSrc='img/icon_minimize.gif';
                actionString='+'+sectionId;
        }
        else
        {
                newState='none';
                newIconSrc='img/icon_maximize.gif';
                actionString='-'+sectionId;
        }

        section.style.display=newState;

        actionBlock=document.getElementById('actionBlock_'+sectionId);
        actionBlock.style.display=oldState;
        
        actionBlockReverse=document.getElementById('actionBlockReverse_'+sectionId);
        actionBlockReverse.style.display=newState;
        
        
        // cookie:
        actionsString=cookieVal('sectionActions');
        actionsString+=actionString+'|';
        document.cookie ="sectionActions="+actionsString+"; path=/admin/";
        return false;
}
