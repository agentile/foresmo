<?php
if ($this->search_adapter == 'Google') {
?>
<div id="cse-search-results"></div>
<script type="text/javascript">
  var googleSearchIframeName = "cse-search-results";
  var googleSearchFormName = "cse-search-box";
  var googleSearchFrameWidth = 670;
  var googleSearchDomain = "www.google.com";
  var googleSearchPath = "/cse";
</script>
<script type="text/javascript">
(function(){var e=null,b=window,o=b.googleSearchResizeIframe||b.googleSearchPath&&b.googleSearchPath=="/cse"&&typeof b.googleSearchResizeIframe=="undefined",l,k,h;function p(a,c,d,i){var f={};a=a.split(d);for(d=0;d<a.length;d++){var g=a[d],j=g.indexOf(c);if(j>0){var m=g.substring(0,j);m=i?m.toUpperCase():m.toLowerCase();g=g.substring(j+1,g.length);f[m]=g}}return f}function r(){var a=document.location.search;if(a.length<1)return"";a=a.substring(1,a.length);a=p(a,"=","&",false);if(b.googleSearchQueryString!=
"q"&&a[b.googleSearchQueryString]){a.q=a[b.googleSearchQueryString];delete a[b.googleSearchQueryString]}if(a.cof){var c=p(decodeURIComponent(a.cof),":",";",true);if(c=c.FORID)l=parseInt(c,10)}if(c=document.getElementById(b.googleSearchFormName)){if(c.q&&a.q&&(!a.ie||a.ie.toLowerCase()=="utf-8"))c.q.value=decodeURIComponent(a.q.replace(/\+/g," "));if(c.sitesearch)for(var d=0;d<c.sitesearch.length;d++)c.sitesearch[d].checked=a.sitesearch==e&&c.sitesearch[d].value==""?true:c.sitesearch[d].value==a.sitesearch?
true:false}c="";for(var i in a)c+="&"+i+"="+a[i];return c.substring(1,c.length)}function n(a,c){return c?"&"+a+"="+encodeURIComponent(c):""}function q(a,c){return a?Math.max(a,c):c}function s(){var a="http://";a+=b.googleSearchDomain?b.googleSearchDomain:"www.google.com";a+=b.googleSearchPath?b.googleSearchPath:"/custom";a+="?";if(b.googleSearchQueryString)b.googleSearchQueryString=b.googleSearchQueryString.toLowerCase();a+=r();a+=n("ad","w"+k);a+=n("num",h);a+=n("adtest",b.googleAdtest);if(o){var c=
b.location.href,d=c.indexOf("#");if(d!=-1)c=c.substring(0,d);a+=n("rurl",c)}return a}function t(){(k=b.googleSearchNumAds)||(k=9);h=(h=b.googleNumSearchResults)?Math.min(h,20):10;var a={};a[9]=795;a[10]=670;a[11]=500;var c={};c[9]=300+90*h;c[10]=300+50*Math.min(k,4)+90*h;c[11]=300+50*k+90*h;var d=s();if(!b.googleSearchFrameborder)b.googleSearchFrameborder="0";var i=document.getElementById(b.googleSearchIframeName);if(i&&a[l]){a=q(b.googleSearchFrameWidth,a[l]);c=q(b.googleSearchFrameHeight,c[l]);
var f=document.createElement("iframe");d={name:"googleSearchFrame",src:d,frameBorder:b.googleSearchFrameborder,width:a,height:c,marginWidth:"0",marginHeight:"0",hspace:"0",vspace:"0",allowTransparency:"true",scrolling:"no"};for(var g in d)f.setAttribute(g,d[g]);i.appendChild(f);f.attachEvent?f.attachEvent("onload",function(){window.scrollTo(0,0)}):f.addEventListener("load",function(){window.scrollTo(0,0)},false);o&&b.setInterval(function(){if(b.location.hash&&b.location.hash!="#"){var j=b.location.hash.substring(1)+
"px";if(f.height!=j&&j!="0px")f.height=j}},10)}b.googleSearchIframeName=e;b.googleSearchFormName=e;b.googleSearchResizeIframe=e;b.googleSearchQueryString=e;b.googleSearchDomain=e;b.googleSearchPath=e;b.googleSearchFrameborder=e;b.googleSearchFrameWidth=e;b.googleSearchFrameHeight=e;b.googleSearchNumAds=e;b.googleNumSearchResults=e;b.googleAdtest=e}t()})();
</script>

<?php
} else {
?>

<!--show-results-->
<?php
}
?>