<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
                exclude-result-prefixes="sitemap">

    <xsl:output method="html" indent="yes" encoding="UTF-8"/>

    <xsl:template match="/">
        <html>
            <head>
                <title>XML Sitemap</title>
                <style>
                    body { font-family: Arial, sans-serif; background-color: #f8f9fa; padding: 20px; color: #333; }
                    .container { max-width: 900px; margin: 0 auto; background: #fff; border: 1px solid #ddd; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
                    h1 { font-size: 24px; color: #212529; }
                    p { color: #555; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { padding: 12px 15px; border: 1px solid #dee2e6; text-align: left; }
                    thead th { background-color: #8BC34A; color: white; font-weight: bold; }
                    tbody tr:nth-child(odd) { background-color: #f9f9f9; }
                    tbody tr:nth-child(even) { background-color: #e9e9e9; }
                    a { color: #0d6efd; text-decoration: none; }
                    a:hover { text-decoration: underline; }
                </style>
            </head>
            <body>
                <div class="container">
                    <h1>XML Sitemap</h1>
                    <p>This sitemap contains <xsl:value-of select="count(sitemap:urlset/sitemap:url)"/> URLs.</p>
                    <table>
                        <thead>
                            <tr>
                                <th>URL</th>
                                <th>Last Updated</th>
                                <th>Change Frequency</th>
                                <th>Priority</th>
                            </tr>
                        </thead>
                        <tbody>
                            <xsl:for-each select="sitemap:urlset/sitemap:url">
                                <tr>
                                    <td>
                                        <xsl:variable name="loc" select="sitemap:loc"/>
                                        <a href="{$loc}"><xsl:value-of select="$loc"/></a>
                                    </td>
                                    <td><xsl:value-of select="sitemap:lastmod"/></td>
                                    <td><xsl:value-of select="sitemap:changefreq"/></td>
                                    <td><xsl:value-of select="sitemap:priority"/></td>
                                </tr>
                            </xsl:for-each>
                        </tbody>
                    </table>
                </div>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
