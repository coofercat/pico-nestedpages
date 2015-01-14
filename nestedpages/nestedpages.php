<?php

/*  Nested Pages (Pico Plugin) - Allow one MD page to include another
    (C) January 2015 Ralph Bolton <ralph@coofercat.com>
    Licensed however you like (although I'd appreciate some credit
    in the comments at least, if you can manage it)

    Adds a custom MD tag of @[...] which includes the contents of
    another page into the current one. For example:

    hello @[/content/otherpage.md] there
*/

class Nestedpages {
  
  public function before_parse_content(&$content)
  {
    // Look for an unescaped @[...] (not \@[...] or @\[...\] or @[...\])
    // "not \, @[, not ] for a long time, not \], ]"
    preg_match_all('/[^\\\]@\[([^\]]*[^\\\])\]/', $content, $matches);
    foreach($matches as $match) {
      foreach($match as $path) {
        # All paths specified have to be relative to the document root.
        # Thus, most paths will be something like /content/bla. We're
        # pretty basic at avoiding "/content/../../../../../etc/passwd"
        # because we jus strip out any '..' in the path. We also don't
        # add the ".md" to the path - that means you can include any
        # file in your page, although it'll get MD rendered. It means
        # you can use sub-pages that aren't considered pages by Pico
        # though.
        $full_path = preg_replace('/\.\./','', CONTENT_DIR . "/$path");
        $full_path = preg_replace('/\/+/','/', $full_path);
        if(file_exists($full_path)) {
          $sub = file_get_contents($full_path);
          # Now sub it into the original
          $pattern = preg_quote($path, '/');
          $content = preg_replace("/@\[$pattern\]/", $sub, $content);
        }
      }
    }
  }
}

// This is for Vim users - please don't delete it
// vim: set filetype=php expandtab tabstop=2 shiftwidth=2:
