<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:str="http://exslt.org/strings"
 xmlns:func="http://exslt.org/functions"
 extension-element-prefixes="exsl func"
>

  <xsl:output method="html" encoding="iso-8859-1"/>

  <func:function name="func:first-sentence">
    <xsl:param name="comment"/>
    
    <func:result>
      <xsl:value-of select="exsl:node-set(str:tokenize($comment, '.&#10;'))"/>
    </func:result>
  </func:function>
  
  <func:function name="func:ltrim">
    <xsl:param name="text"/>
    <xsl:param name="chars"/>
    
    <func:result>
      <xsl:choose>
        <xsl:when test="contains(substring($text, 1, 1), $chars)">
          <xsl:value-of select="func:ltrim(substring($text, 2, string-length($text)), $chars)"/>
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of select="$text"/>
        </xsl:otherwise>
      </xsl:choose>
    </func:result>
  </func:function>
  
  <func:function name="func:cutstring">
    <xsl:param name="text"/>
    <xsl:param name="maxlength"/>
    
    <func:result>
      <xsl:choose>
        <xsl:when test="string-length($text) &lt;= $maxlength">
          <xsl:value-of select="$text"/>
        </xsl:when>
        <xsl:otherwise>
          <span title="{$text}">
            <xsl:value-of select="substring($text, 1, $maxlength)"/>
            <b>[...]</b>
          </span>
        </xsl:otherwise>
      </xsl:choose>
    </func:result>
  </func:function>
  
  <func:function name="func:typelink">
    <xsl:param name="type"/>
    
    <func:result>
      <a>
        <xsl:if test="contains($type, '.')">
          <xsl:attribute name="href">
            <xsl:value-of select="concat('?class:', string(exsl:node-set(str:tokenize(func:ltrim($type, '&amp;'), '[&amp;'))))"/>
          </xsl:attribute>
        </xsl:if>
        
        <xsl:value-of select="$type"/>
      </a>
    </func:result>
  </func:function>

  <xsl:template match="comment//*">
    <xsl:copy>
      <xsl:copy-of select="@*"/>
      <xsl:apply-templates/>
    </xsl:copy>
  </xsl:template>

  <xsl:template match="class">
    <style type="text/css">
      h2 { margin-top: 30px; }
      h3 { margin-top: 20px; }
      h4 { font: bold 13px "Trebuchet MS", "Arial", sans-serif; margin-top: 0px; }
      hr { border: 0; background-color: #cccccc; height: 1px; }
      fieldset {
        margin-top: 20px;
        border: 1px solid #3165c5;
      }
      legend {
        font: bold 13px "Trebuchet MS", "Arial", sans-serif;
        color: #3165c5;
      }
      fieldset.warning {
        border: 1px solid #ffa800;
        background: #ffeac0 url('image/deprecated.png') 10px 10px no-repeat;
      }
      fieldset.warning p {
        margin-left: 40px;
      }
      fieldset.hint {
        border: 1px solid #001f83;
        background: #e2e7f8 url('image/unittest.png') 10px 10px no-repeat;
      }
      fieldset.hint p {
        margin-left: 40px;
      }
      fieldset.hint a {
        color: #001f83;
      }
      fieldset.warning em {
        color: #963817;
        font-weight: bold;
      }
      #content ul {
        list-style-type: square;
        list-style-image: url(image/li.gif);
        line-height: 18px;
      }
      code {
        display: block;
        white-space: pre;
      }
      p.annotations {
        font-family: "Lucida console", "Lucida", "Courier new", monospace;
        color: #3165c5;
        margin: 0px;
      }
      p.comment {
        color: #444444;
      }
      a.class {
        background: url(image/arrow.png);
        background-position: right center; 
        background-repeat: no-repeat;
        padding-right: 20px;
      }
    </style>
    <h1>
      <xsl:for-each select="modifiers/*">
        <xsl:value-of select="name()"/>
        <xsl:text> </xsl:text>
      </xsl:for-each>
      <xsl:value-of select="concat(@type, ' ', @name)"/>
    </h1>

    <!-- Deprecation note -->
    <xsl:if test="deprecated">
      <fieldset class="warning">
        <p>
          <b>This class has been marked as deprecated.</b>
          Usage is discouraged though this class remains in the framework 
          for backward compatibility.<br/><br/>
          <em>
            <xsl:value-of select="deprecated" disable-output-escaping="yes"/>
          </em>
        </p>
      </fieldset>
    </xsl:if>

    <!-- Unittests -->
    <xsl:if test="count(test) &gt; 0">
      <fieldset class="hint">
        <p>
          This class' functionality is verified by the following tests:<br/>
          <xsl:for-each select="test">
            <xsl:variable name="class" select="substring-after(@href, 'xp://')"/>
            <a class="class" href="?class:{$class}"><xsl:value-of select="$class"/></a>
            <xsl:if test="position() != last()">, </xsl:if>
          </xsl:for-each>
        </p>
      </fieldset>
    </xsl:if>
    
    <!-- Final -->
    <xsl:if test="modifiers/final">
      <fieldset class="hint">
        <p>
          This class is declared as final - it cannot be overwritten!<br/>
          &#160;<br/>
        </p>
      </fieldset>
    </xsl:if>

    <!-- Abstract -->
    <xsl:if test="modifiers/abstract">
      <fieldset class="hint">
        <p>
          This class is declared as abstract - it must be overwritten!<br/>
          &#160;<br/>
        </p>
      </fieldset>
    </xsl:if>

    <h2>Purpose: <xsl:value-of select="purpose"/></h2>
    <div class="apidoc">
      <xsl:apply-templates select="comment"/>
    </div>

    <h2>Inheritance</h2>
    <p>
      <a><xsl:value-of select="@name"/></a>
      <xsl:for-each select="extends/link">
        &#xbb; <a href="?class:{@href}"><xsl:value-of select="@href"/></a>
      </xsl:for-each>
    </p>

    <xsl:if test="count(implements/link) &gt; 0">
      <h2>Implemented Interfaces</h2>
      <p>
        <xsl:for-each select="implements/link">
          <a href="?class:{@href}"><xsl:value-of select="@href"/></a>
          <xsl:if test="position() != last()">, </xsl:if>
        </xsl:for-each>
      </p>
    </xsl:if>

    <!-- Constants -->
    <a name="__constants"/>
    <xsl:if test="count(constant) &gt; 0">
      <h2>Constants</h2>
      <ul>
        <xsl:for-each select="constant">
          <li>
            <a name="{@name}"><b><xsl:value-of select="@name"/></b></a>
            <xsl:if test="string(.) != ''"><tt>= <xsl:copy-of select="func:cutstring(., 72)"/></tt></xsl:if>
          </li>
        </xsl:for-each>
      </ul>
    </xsl:if>
    
    <h2>Members</h2>

    <!-- Fields -->
    <a name="__fields"/>
    <fieldset>
      <legend>Field summary</legend>
      <xsl:choose>
        <xsl:when test="count(fields[not(@from)]/field) &gt; 0">
          <h3>Fields declared in this class</h3>
          <ul>
            <xsl:for-each select="fields[not(@from)]/field">
              <li>
                <a name="{@name}"><b>
                  <xsl:for-each select="modifiers/*">
                    <xsl:value-of select="name()"/>
                    <xsl:text> </xsl:text>
                  </xsl:for-each>
                  <xsl:value-of select="@name"/>
                </b></a>
                <xsl:if test="string(constant) != ''"><tt>= <xsl:value-of select="constant"/></tt></xsl:if>
              </li>
            </xsl:for-each>
          </ul>
        </xsl:when>
        <xsl:otherwise>
          <em>(This class does not declare any fields)</em>
        </xsl:otherwise>
      </xsl:choose>

      <!-- Inherited fields -->
      <xsl:for-each select="fields[@from]">
        <xsl:if test="count(field) &gt; 0">
          <h3>Fields inherited from <a href="?class:{@from}"><xsl:value-of select="@from"/></a></h3>

          <p>
            <xsl:for-each select="field">
              <a href="?class:{../@from}#{@name}"><xsl:value-of select="@name"/></a>
              <xsl:if test="position() != last()">, </xsl:if>
            </xsl:for-each>
          </p>
        </xsl:if>
      </xsl:for-each>
    </fieldset>

    <!-- Methods -->
    <a name="__methods"/>
    <fieldset>
      <legend>Method summary</legend>
      <xsl:choose>
        <xsl:when test="count(methods[not(@from)]/method) &gt; 0">
          <h3>Methods declared in this class</h3>
          <ul>
            <xsl:for-each select="methods[not(@from)]/method">
              <li>
                <a href="#{@name}">
                  <xsl:value-of select="concat(@access, ' ', @return)"/>
                  <xsl:text> </xsl:text><b><xsl:value-of select="@name"/></b>
                  <xsl:text>(</xsl:text>
                  <xsl:for-each select="argument">
                    <xsl:value-of select="@name"/>
                    <xsl:if test="position() != last()">, </xsl:if>
                  </xsl:for-each>
                  <xsl:text>)</xsl:text>
                </a><br/>
                <em><xsl:value-of select="func:first-sentence(comment)" disable-output-escaping="yes"/></em>
              </li>
            </xsl:for-each>
          </ul>
        </xsl:when>
        <xsl:otherwise>
          <em>(This class does not declare any methods)</em>
        </xsl:otherwise>
      </xsl:choose>

      <!-- Inherited methods -->
      <xsl:for-each select="methods[@from]">
        <xsl:if test="count(method) &gt; 0">
          <h3>Methods inherited from <a href="?class:{@from}"><xsl:value-of select="@from"/></a></h3>

          <p>
            <xsl:for-each select="method">
              <a href="?class:{../@from}#{@name}"><xsl:value-of select="@name"/>()</a>
              <xsl:if test="position() != last()">, </xsl:if>
            </xsl:for-each>
          </p>
        </xsl:if>
      </xsl:for-each>
    </fieldset>

    <h2>Method details</h2>
    <xsl:for-each select="methods[not(@from)]/method">
      <a name="{@name}"/>
      <p class="annotations">
        <xsl:for-each select="annotations/annotation">
          <xsl:value-of select="concat('@', @name, '(', value, ')')"/>
          <xsl:if test="position() != last()">, </xsl:if>
        </xsl:for-each>
        &#160;
      </p>
      <h4>
        <xsl:for-each select="modifiers/*">
          <xsl:value-of select="name()"/>
          <xsl:text> </xsl:text>
        </xsl:for-each>
        <xsl:text> </xsl:text>
        <a>
          <xsl:if test="contains(@return, '.')"><xsl:attribute name="href">
            <xsl:text>?</xsl:text>
            <xsl:value-of select="func:ltrim(substring-before(concat(@return, '['), '['), '&amp;')"/>
          </xsl:attribute></xsl:if>
          <xsl:value-of select="@return"/>
        </a>
        <xsl:text> </xsl:text>
        <xsl:value-of select="@name"/>
        <xsl:text>(</xsl:text>
        <xsl:for-each select="argument">
          <xsl:value-of select="concat(@type, ' ', @name)"/>
          <xsl:if test="position() != last()">, </xsl:if>
        </xsl:for-each>
        <xsl:text>)</xsl:text>
      </h4>
      <div class="apidoc">
        <xsl:apply-templates select="comment"/>
      </div>
      
      <xsl:if test="count(argument) &gt; 0">
        <h4>Arguments:</h4>
        <ul>
          <xsl:for-each select="argument">
            <li>
              <xsl:copy-of select="func:typelink(@type)"/>
              <xsl:text> </xsl:text>
              <xsl:value-of select="@name"/>
              <xsl:if test="string(default) != ''"><tt>= <xsl:value-of select="default"/></tt></xsl:if>
            </li>
          </xsl:for-each>
        </ul>
      </xsl:if>

      <xsl:if test="count(exception) &gt; 0">
        <h4>Exceptions:</h4>
        <ul>
          <xsl:for-each select="exception">
            <li>
              <a href="?class:{@class}"><xsl:value-of select="@class"/></a>
            </li>
          </xsl:for-each>
        </ul>
      </xsl:if>

      <xsl:if test="count(see) &gt; 0">
        <h4>See also</h4>

        <ul>
          <xsl:for-each select="see">
            <li>
              <xsl:apply-templates select="."/>
            </li>
          </xsl:for-each>
        </ul>
      </xsl:if>
      
      <hr/>
    </xsl:for-each>
  </xsl:template>
  
  <xsl:template match="see[@scheme = 'xp']" mode="short">
    <a href="?class:{@href}"><xsl:copy-of select="func:cutstring(@href, 24)"/></a>
  </xsl:template>

  <xsl:template match="see[@scheme = 'php']" mode="short">
    <a href="http://php3.de/{@href}"><xsl:copy-of select="func:cutstring(@href, 24)"/></a>
  </xsl:template>

  <xsl:template match="see[@scheme = 'http']" mode="short">
    <a href="http://{@href}"><xsl:copy-of select="func:cutstring(@href, 24)"/></a>
  </xsl:template>

  <xsl:template match="see[@scheme = 'xp']">
    <a href="?class:{@href}"><xsl:value-of select="@href"/></a>
  </xsl:template>

  <xsl:template match="see[@scheme = 'php']">
    <a href="http://php3.de/{@href}"><xsl:value-of select="@href"/></a>
  </xsl:template>

  <xsl:template match="see[@scheme = 'http']">
    <a href="http://{@href}"><xsl:value-of select="@href"/></a>
  </xsl:template>

  <xsl:template match="/">
    <html><head><link rel="stylesheet" href="style.css"/></head><body>
    <div id="search">
      <form action="/search">
        <label for="query"><u>S</u>earch XP website for </label>
        <input name="query" accesskey="s" type="text"></input>
      </form>
    </div>
    <div id="top">&#160;
    </div>
    <div id="menu">
      <ul>
        <li><a href="home.html">Home</a></li>
        <li><a href="news.html">News</a></li>
        <li id="active"><a href="?">Documentation</a></li>
        <li><a href="download.html">Download</a></li>
        <li><a href="dev.html">Developers</a></li>
      </ul>
      <!-- For Mozilla to calculate height correctly -->
      &#160;
    </div>
    <table id="main" cellpadding="0" cellspacing="10"><tr>
      <td id="content">

        <xsl:apply-templates select="doc"/>
        
      </td>
      <td id="context">
        <h3>Navigation</h3>
        <a href="?package:{doc/class/@package}"><xsl:value-of select="doc/class/@package"/></a><br/>

        <h3>Jump to</h3>
        <a href="#__constants">Constants</a><br/>
        <a href="#__fields">Fields</a><br/>
        <a href="#__methods">Methods</a><br/>

        <h3>Source</h3>
        <a href="source.php?{doc/class/@name}">View</a><br/>

        <xsl:if test="count(doc/class/see) &gt; 0">
          <h3>See also</h3>
          
          <xsl:for-each select="doc/class/see">
            <xsl:apply-templates select="." mode="short"/>
            <br/>
          </xsl:for-each>
        </xsl:if>
      </td>
    </tr></table>
    <div id="footer">
      <a href="credits.html">Credits</a> |
      <a href="feedback.html">Feedback</a>
      
      <br/>
      
      (c) 2001-2007 the XP team
    </div>
    </body></html>
  </xsl:template>
</xsl:stylesheet>
