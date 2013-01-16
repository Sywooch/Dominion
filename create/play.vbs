Set fso=CreateObject("Scripting.FileSystemObject")

Set hfile=fso.CreateTextFile("create.sql")
hfile.WriteLine(ParseDoc("config.xml", "tools/createtable.xsl"))
hfile.WriteLine(ParseDoc("config.xml", "tools_multilanguage/createtable.xsl"))
hfile.Close

Set hfile=fso.CreateTextFile("tmp.tpl")
hfile.WriteLine(ParseDoc("config.xml", "tools/createscripts.xsl"))
hfile.WriteLine(ParseDoc("config.xml", "tools_multilanguage/createscripts.xsl"))
hfile.Close

Set hfile=fso.CreateTextFile("makedir.bat")
hfile.WriteLine(ParseDoc("config.xml", "tools_common/imagedir.xsl"))
hfile.Close

Function ParseDoc(XMLDocFileName, XSLDocFileName)
Set source = CreateObject("MSXML2.FreeThreadedDOMDocument.4.0")'Msxml2.DOMDocument.4.0")
'MSXML2.DOMDocument
    Source.Async = False
    Source.Load(XMLDocFileName)
Set style = CreateObject("MSXML2.FreeThreadedDOMDocument.4.0")
    Style.Async = False
    Style.Load(XSLDocFileName)
ParseDoc = source.transformNode(style)
End Function


