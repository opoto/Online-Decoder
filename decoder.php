<?php

/* === XML FORMATTING FUNCTION === */

function formatXmlString1($xml) {  
  $dom = new DOMDocument($xml);
  return $dom->dump_mem(true, 'UTF-8');
}

function formatXmlString2($xml) {  
  
  // add marker linefeeds to aid the pretty-tokeniser (adds a linefeed between all tag-end boundaries)
  $xml = preg_replace('/(>)(<)(\/*)/', "$1\n$2$3", $xml);
  
  // now indent the tags
  $token      = strtok($xml, "\n");
  $result     = ''; // holds formatted version as it is built
  $pad        = 0; // initial indent
  $matches    = array(); // returns from preg_matches()
  
  // scan each line and adjust indent based on opening/closing tags
  while ($token !== false) : 
  
    // test for the various tag states
    
    // 1. open and closing tags on same line - no change
    if (preg_match('/.+<\/\w[^>]*>$/', $token, $matches)) : 
      $indent=0;
    // 2. closing tag - outdent now
    elseif (preg_match('/^<\/\w/', $token, $matches)) :
      $pad -= 3;
      $indent = 0;
    // 3. opening tag - don't pad this one, only subsequent tags
    elseif (preg_match('/^<\w[^>]*[^\/]>.*$/', $token, $matches)) :
      $indent = 2;
    // 4. no indentation needed
    else :
      $indent = 0; 
    endif;
    
    // pad the line with the required number of leading spaces
    $line    = str_pad($token, strlen($token)+$pad, ' ', STR_PAD_LEFT);
    $result .= $line . "\n"; // add to the cumulative result, with linefeed
    $token   = strtok("\n"); // get the next token
    $pad    += $indent; // update the pad size for subsequent lines    
  endwhile; 
  
  return $result;
}

function formatXmlString3($xml){
    $xml = preg_replace('/(>)(<)(\/*)/', "$1\n$2$3", $xml);
    $token      = strtok($xml, "\n");
    $result     = '';
    $pad        = 0; 
    $matches    = array();
    while ($token !== false) : 
        if (preg_match('/.+<\/\w[^>]*>$/', $token, $matches)) : 
          $indent=0;
        elseif (preg_match('/^<\/\w/', $token, $matches)) :
          $pad--;
          $indent = 0;
        elseif (preg_match('/^<\w[^>]*[^\/]>.*$/', $token, $matches)) :
          $indent = 1;
        else :
          $indent = 0; 
        endif;
        $line    = str_pad($token, strlen($token)+$pad, ' ', STR_PAD_LEFT);
        $result .= $line . "\n";
        $token   = strtok("\n");
        $pad    += $indent;
    endwhile; 
    return $result;
}

?>
<html>
<head>
<title>Online decoder</title>
<script src="codemirror/codemirror.js"></script>
<link rel="stylesheet" href="codemirror/codemirror.css">
<script src="codemirror/xml.js"></script>
</head>
<body>
<h1>Online decoder</h1>
Text to decode:
<form method="post">
  <textarea name="todecode" rows="10" cols="80" style="width: 100%;"><?= $todecode ?></textarea>
  <br/>
  Input encoding: 
  (<input type="checkbox" name="inURL" value="true" <?= ($inURL != null) ? "checked" : "" ?> /> URL
  (<input type="checkbox" name="inB64" value="true" <?= ($inB64 != null) ? "checked" : "" ?> /> Base64
  (<input type="checkbox" name="inZIP" value="true" <?= ($inZIP != null) ? "checked" : "" ?> /> Zipped)))
  <input type="submit" name="decode" value="Decode"/>
  <!-- input type="submit" name="b64cert" value="Decode B64 certificate"/><br/ -->
<?php
  $outTxt = ($out == null) || ($out == 'outTxt') ? "checked" : "";
  $outXml = ($out != null) && ($out == 'outXml') ? "checked" : "";
?>
  =&gt; Output:
  <input type="radio" name="out" value="outTxt" <?=$outTxt?> > Raw text 
  <input type="radio" name="out" value="outXml" <?=$outXml?> > Formatted XML<br>
</form>
<?php

if ($todecode != null) {

    $res = $todecode;
    if ($inURL!= null) {
      $res = urldecode($res);
    }
    if ($inB64!= null) {
      $res = base64_decode($res);
    }
    if ($inZIP!= null) {
      $res = gzinflate($res);
    }

    if ($out == "outXml") {
      $res = formatXmlString3($res);
      $res = htmlspecialchars($res);
?>
  <textarea id="outArea" rows="15" cols="80" style="width: 100%; height:60%;"><?= $res?></textarea>
  <script type="text/javascript">
      var editor = CodeMirror.fromTextArea(document.getElementById("outArea"), {
        mode: {name: "xml", alignCDATA: true},
        lineNumbers: true
      });
      // set area size
      var ed_style = editor.getScrollerElement().style
      ed_style.width="100%"
      ed_style.height="60%"
      ed_style.border="1px solid"
  </script>
<?php

    } else {
?>
  <textarea id="outArea" rows="15" cols="80" style="width: 100%; height:60%;"><?= $res?></textarea>
<?php
    }
}
?>
<hr/>
<script language="JavaScript" src="showmail.js"></script>
&copy; <a href="javascript:doEmail('gmail.com', 'olivier.potonniee')">Olivier Potonni&eacute;e</a> 2011

<?php
    $link = "http://olivier.potonniee.free.fr/decoder.php";
?>
<!-- Facebook: --
<!-- See http://developers.facebook.com/docs/reference/plugins/like/ -->
<iframe src="//www.facebook.com/plugins/like.php?href=<?= urlencode($link) ?>&amp;send=false&amp;layout=button_count&amp;width=90&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=21" scrolling="no" frameborder="0" style="margin-left:30px; border:none; overflow:hidden; width:100px; height:21px;" allowTransparency="true"></iframe>


<!-- Google +1: -->
<!-- See http://www.google.com/webmasters/+1/button/ -->
<!-- Place this tag where you want the +1 button to render -->
<g:plusone size="medium" href="<?= $link ?>"></g:plusone>

<!-- Place this render call where appropriate -->
<script type="text/javascript">
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>

<!-- Tweet: -->
<!-- See http://twitter.com/about/resources/buttons#tweet -->
<!-- See https://dev.twitter.com/docs/tweet-button -->
<a href="https://twitter.com/share" class="twitter-share-button" data-url="<?= $link ?>">Tweet</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>

</body>
</html>