<?php

abstract class App_Controller_Helper_HelperAbstract
{

    /**
     * Data model
     *
     * @var ZendDBEntity
     */
    protected $work_model;

    /**
     * DOM xml data
     *
     * @var DOMxml
     */
    protected $domXml;
    protected $params;
    protected $rules;
    protected $lang = '';
    protected $lang_id = 0;
    protected $monthes = array('01' => 'января',
        '02' => 'февраля',
        '03' => 'марта',
        '04' => 'апреля',
        '05' => 'мая',
        '06' => 'июня',
        '07' => 'июля',
        '08' => 'августа',
        '09' => 'сентября',
        '10' => 'октября',
        '11' => 'ноября',
        '12' => 'декабря',);

    public function __construct($params = array())
    {
        $this->params = $params;

        $another_pages = new models_AnotherPages();
//      $this->rules = $another_pages->translite4rules();
    }

    public function setModel($work_model)
    {
        $this->work_model = $work_model;
    }

    public function setLang($lang, $lang_id)
    {
        $this->lang = $lang;
        $this->lang_id = $lang_id;
    }

    public function setDomXml($domXml)
    {
        $this->domXml = $domXml;
    }

    public function getDomXml()
    {
        return $this->domXml;
    }

    public function setXmlNode($doc, $tag = '')
    {
        $doc = stripslashes($doc);
        $doc = $this->removeHTMLentity($doc);
        if (!empty($doc)) {
            if ($tag) {
                $txt = "<?xml version=\"1.0\" encoding=\"{$this->domXml->get_encoding()}\"?><!DOCTYPE stylesheet SYSTEM \"symbols.ent\"><$tag>" . $doc . "</$tag>";
            } else {
                $txt = "<?xml version=\"1.0\" encoding=\"{$this->domXml->get_encoding()}\"?><!DOCTYPE stylesheet SYSTEM \"symbols.ent\"><txt>" . $doc . "</txt>";
            }
            $this->domXml->import_node($txt, false);
        }
    }

    protected function truncate($string, $length = 80, $etc = '...',
                                $break_words = false, $middle = false)
    {
        if ($length == 0)
            return '';

        if (strlen($string) > $length) {
            $length -= strlen($etc);
            if (!$break_words && !$middle) {
                $string = preg_replace('/\s+?(\S+)?$/', '',
                    substr($string, 0, $length + 1));
            }
            if (!$middle) {
                return substr($string, 0, $length) . $etc;
            } else {
                return substr($string, 0, $length / 2) . $etc . substr($string,
                    -$length / 2);
            }
        } else {
            return $string;
        }
    }

    private function removeHTMLentity($xml)
    {
        $search = array(
            "'&nbsp;'i", "'&amp;'i", "'&quot;'i", "'&cent;'i", "'&euro;'i", "'&pound;'i", "'&yen;'i", "'&copy;'i", "'&reg;'i", "'&trade;'i",
            "'&permil;'i", "'&micro;'i", "'&middot;'i", "'&bull;'i", "'&hellip;'i", "'&prime;'i", "'&Prime;'i", "'&sect;'i", "'&para;'i", "'&szlig;'i",
            "'&lsaquo;'i", "'&rsaquo;'i", "'&laquo;'i", "'&raquo;'i", "'&lsquo;'i", "'&rsquo;'i", "'&ldquo;'i", "'&rdquo;'i", "'&sbquo;'i", "'&bdquo;'i",
            "'&lt;'i", "'&gt;'i", "'&le;'i", "'&ge;'i", "'&ndash;'i", "'&mdash;'i", "'&macr;'i", "'&oline;'i", "'&curren;'i", "'&brvbar;'i",
            "'&uml;'i", "'&iexcl;'i", "'&iquest;'i", "'&circ;'i", "'&tilde;'i", "'&deg;'i", "'&minus;'i", "'&plusmn;'i", "'&divide;'i", "'&frasl;'i",
            "'&times;'i", "'&sup1;'i", "'&sup2;'i", "'&sup3;'i", "'&frac14;'i", "'&frac12;'i", "'&frac34;'i", "'&fnof;'i", "'&int;'i", "'&sum;'i",
            "'&infin;'i", "'&radic;'i", "'&sim;'i", "'&cong;'i", "'&asymp;'i", "'&ne;'i", "'&equiv;'i", "'&isin;'i", "'&notin;'i", "'&ni;'i",
            "'&prod;'i", "'&and;'i", "'&or;'i", "'&not;'i", "'&cap;'i", "'&cup;'i", "'&part;'i", "'&forall;'i", "'&exist;'i", "'&empty;'i",
            "'&nabla;'i", "'&lowast;'i", "'&prop;'i", "'&ang;'i", "'&acute;'i", "'&cedil;'i", "'&ordf;'i", "'&ordm;'i", "'&dagger;'i", "'&Dagger;'i",
            "'&Agrave;'i", "'&Aacute;'i", "'&Acirc;'i", "'&Atilde;'i", "'&Auml;'i", "'&Aring;'i", "'&AElig;'i", "'&Ccedil;'i", "'&Egrave;'i", "'&Eacute;'i",
            "'&Ecirc;'i", "'&Euml;'i", "'&Igrave;'i", "'&Iacute;'i", "'&Icirc;'i", "'&Iuml;'i", "'&ETH;'i", "'&Ntilde;'i", "'&Ograve;'i", "'&Oacute;'i",
            "'&Ocirc;'i", "'&Otilde;'i", "'&Ouml;'i", "'&Oslash;'i", "'&OElig;'i", "'&Scaron;'i", "'&Ugrave;'i", "'&Uacute;'i", "'&Ucirc;'i", "'&Uuml;'i",
            "'&Yacute;'i", "'&Yuml;'i", "'&THORN;'i", "'&agrave;'i", "'&aacute;'i", "'&acirc;'i", "'&atilde;'i", "'&auml;'i", "'&aring;'i", "'&aelig;'i",
            "'&ccedil;'i", "'&egrave;'i", "'&eacute;'i", "'&ecirc;'i", "'&euml;'i", "'&igrave;'i", "'&iacute;'i", "'&icirc;'i", "'&iuml;'i", "'&eth;'i",
            "'&ntilde;'i", "'&ograve;'i", "'&oacute;'i", "'&ocirc;'i", "'&otilde;'i", "'&ouml;'i", "'&oslash;'i", "'&oelig;'i", "'&scaron;'i", "'&ugrave;'i",
            "'&uacute;'i", "'&ucirc;'i", "'&uuml;'i", "'&yacute;'i", "'&thorn;'i", "'&yuml;'i", "'&Alpha;'i", "'&Beta;'i", "'&Gamma;'i", "'&Delta;'i",
            "'&Epsilon;'i", "'&Zeta;'i", "'&Eta;'i", "'&Theta;'i", "'&Iota;'i", "'&Kappa;'i", "'&Lambda;'i", "'&Mu;'i", "'&Nu;'i", "'&Xi;'i",
            "'&Omicron;'i", "'&Pi;'i", "'&Rho;'i", "'&Sigma;'i", "'&Tau;'i", "'&Upsilon;'i", "'&Phi;'i", "'&Chi;'i", "'&Psi;'i", "'&Omega;'i",
            "'&alpha;'i", "'&beta;'i", "'&gamma;'i", "'&delta;'i", "'&epsilon;'i", "'&zeta;'i", "'&eta;'i", "'&theta;'i", "'&iota;'i", "'&kappa;'i",
            "'&lambda;'i", "'&mu;'i", "'&nu;'i", "'&xi;'i", "'&omicron;'i", "'&pi;'i", "'&rho;'i", "'&sigmaf;'i", "'&sigma;'i", "'&tau;'i",
            "'&upsilon;'i", "'&phi;'i", "'&chi;'i", "'&psi;'i", "'&omega;'i", "'&alefsym;'i", "'&piv;'i", "'&real;'i", "'&thetasym;'i", "'&upsih;'i",
            "'&weierp;'i", "'&image;'i", "'&larr;'i", "'&uarr;'i", "'&rarr;'i", "'&darr;'i", "'&harr;'i", "'&crarr;'i", "'&lArr;'i", "'&uArr;'i",
            "'&rArr;'i", "'&dArr;'i", "'&hArr;'i", "'&there4;'i", "'&sub;'i", "'&sup;'i", "'&nsub;'i", "'&sube;'i", "'&supe;'i", "'&oplus;'i",
            "'&otimes;'i", "'&perp;'i", "'&sdot;'i", "'&lceil;'i", "'&rceil;'i", "'&lfloor;'i", "'&rfloor;'i", "'&lang;'i", "'&rang;'i", "'&loz;'i",
            "'&spades;'i", "'&clubs;'i", "'&hearts;'i", "'&diams;'i", "'&ensp;'i", "'&emsp;'i", "'&thinsp;'i", "'&zwnj;'i", "'&zwj;'i", "'&lrm;'i",
            "'&rlm;'i", "'&shy;'i"
        );

        $replace = array(
            "&#160;", "&#38;", "&#34;", "&#162;", "&#8364;", "&#163;", "&#165;", "&#169;", "&#174;", "&#8482;",
            "&#8240;", "&#181;", "&#183;", "&#8226;", "&#8230;", "&#8242;", "&#8243;", "&#167;", "&#182;", "&#223;",
            "&#8249;", "&#8250;", "&#171;", "&#187;", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8218;", "&#8222;",
            "&#60;", "&#62;", "&#8804;", "&#8805;", "&#8211;", "&#8212;", "&#175;", "&#8254;", "&#164;", "&#166;",
            "&#168;", "&#161;", "&#191;", "&#710;", "&#732;", "&#176;", "&#8722;", "&#177;", "&#247;", "&#8260;",
            "&#215;", "&#185;", "&#178;", "&#179;", "&#188;", "&#189;", "&#190;", "&#402;", "&#8747;", "&#8721;",
            "&#8734;", "&#8730;", "&#8764;", "&#8773;", "&#8776;", "&#8800;", "&#8801;", "&#8712;", "&#8713;", "&#8715;",
            "&#8719;", "&#8743;", "&#8744;", "&#172;", "&#8745;", "&#8746;", "&#8706;", "&#8704;", "&#8707;", "&#8709;",
            "&#8711;", "&#8727;", "&#8733;", "&#8736;", "&#180;", "&#184;", "&#170;", "&#186;", "&#8224;", "&#8225;",
            "&#192;", "&#193;", "&#194;", "&#195;", "&#196;", "&#197;", "&#198;", "&#199;", "&#200;", "&#201;",
            "&#202;", "&#203;", "&#204;", "&#205;", "&#206;", "&#207;", "&#208;", "&#209;", "&#210;", "&#211;",
            "&#212;", "&#213;", "&#214;", "&#216;", "&#338;", "&#352;", "&#217;", "&#218;", "&#219;", "&#220;",
            "&#221;", "&#376;", "&#222;", "&#224;", "&#225;", "&#226;", "&#227;", "&#228;", "&#229;", "&#230;",
            "&#231;", "&#232;", "&#233;", "&#234;", "&#235;", "&#236;", "&#237;", "&#238;", "&#239;", "&#240;",
            "&#241;", "&#242;", "&#243;", "&#244;", "&#245;", "&#246;", "&#248;", "&#339;", "&#353;", "&#249;",
            "&#250;", "&#251;", "&#252;", "&#253;", "&#254;", "&#255;", "&#913;", "&#914;", "&#915;", "&#916;",
            "&#917;", "&#918;", "&#919;", "&#920;", "&#921;", "&#922;", "&#923;", "&#924;", "&#925;", "&#926;",
            "&#927;", "&#928;", "&#929;", "&#931;", "&#932;", "&#933;", "&#934;", "&#935;", "&#936;", "&#937;",
            "&#945;", "&#946;", "&#947;", "&#948;", "&#949;", "&#950;", "&#951;", "&#952;", "&#953;", "&#954;",
            "&#955;", "&#956;", "&#957;", "&#958;", "&#959;", "&#960;", "&#961;", "&#962;", "&#963;", "&#964;",
            "&#965;", "&#966;", "&#967;", "&#968;", "&#969;", "&#8501;", "&#982;", "&#8476;", "&#977;", "&#978;",
            "&#8472;", "&#8465;", "&#8592;", "&#8593;", "&#8594;", "&#8595;", "&#8596;", "&#8629;", "&#8656;", "&#8657;",
            "&#8658;", "&#8659;", "&#8660;", "&#8756;", "&#8834;", "&#8835;", "&#8836;", "&#8838;", "&#8839;", "&#8853;",
            "&#8855;", "&#8869;", "&#8901;", "&#8968;", "&#8969;", "&#8970;", "&#8971;", "&#9001;", "&#9002;", "&#9674;",
            "&#9824;", "&#9827;", "&#9829;", "&#9830;", "&#8194;", "&#8195;", "&#8201;", "&#8204;", "&#8205;", "&#8206;",
            "&#8207;", "&#173;"
        );

        $xml = preg_replace($search, $replace, $xml);

        return $xml;
    }

    protected function translite4rules($cyrStr)
    {
        $cyrStr = mb_convert_case($cyrStr, MB_CASE_LOWER, 'utf-8');

        $latinStr = strtr($cyrStr, $this->rules);
        $latinStr = preg_replace("/\s+/s", '-', $latinStr);
        $latinStr = preg_replace("/-{2,}/", "-", $latinStr);

        $latinStr = strtolower($latinStr);

        return $latinStr;
    }

}