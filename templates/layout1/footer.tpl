</td>
{if $Stephead != "new"}
<td valign="top">
    <b>{$Statistics}</b><br />
    {$Playerslist}:<br /><br />
    {section name=players loop=$List}
        {$List[players]}
    {/section}
    <br /><b>{$Players} </b> <a href="memberlist.php">{$Registeredplayers}</a>.<br />
    <b>{$Online}</b> {$Playersonline}.
</td>
{/if}
</tr>
</table>
{if $Show == "1"}
<div align="center">
    {$Loadingtime}: {$Duration} | {$Gzipcomp}: {$Compress} | {$Pmtime} PHP/MySQL: {$PHPtime}/{$Sqltime} | {$Queries}: {$Numquery} | {$Memory}: {$Memusage} {$MB} <a href="source.php?file={$Filename}" target="_blank">{$Asource}</a></span>
</div>
{/if}
<div align="center">
    &copy; 2004-2012 <a href="https://launchpad.net/vallheru">Vallheru Team</a> based on <a href="http://sourceforge.net/projects/gamers-fusion">Gamers-Fusion 2.5</a>
</div>
</div>
    <!--          (C) 2004,2005,2006,2007,2011,2012 Vallheru Team                         -->
    <!--           game based on code Gamers Fusion ver 2.5                               -->
    </body>
</html>                        


