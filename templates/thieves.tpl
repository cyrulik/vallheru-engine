{if $Step == ""}
    {$Thiefinfo}<br /><br />
    <ul>
        <li><a href="thieves.php?step=monuments">{$Amonuments}</a></li>
        <li><a href="thieves.php?step=shop">{$Aitems}</a></li>
	<li><a href="thieves.php?step=missions">{$Amissions}</a></li>
    </ul>
{/if}
{if $Step == "monuments"}
    {$Minfo}<br /><br />
    {* Each monument group is a separate table. *}
    <table align="center" width="100%">
    <tr>
    {section name=j loop=$Titles}
        {if $smarty.section.j.last && ($smarty.section.j.iteration % 2 == 1)}
{* If its last element, it has to be aligned to center and cover ALL columns. *}
        <td colspan="2" align="center">
        {else}
        <td align="center">
        {/if}
        
{* !!! Display each monument - start. *}
            <table class="td" cellpadding="0" cellspacing="4">
                <tr>
                    <th style="border-bottom: solid gray 1px;" align="center" colspan="2">{$Titles[j]}</th>
                </tr>
                <tr>
                    <td width="100" align="center"><b><u>{$Mname}</u></b></td>
                    <td width="100" align="center"><b><u>{$Descriptions[j]}</u></b></td>
                </tr>
                {section name=k loop=$Monuments[j]}
                    <tr>
                        <td align="left">
                            <a href="view.php?view={$Monuments[j][k].id}">{$Monuments[j][k].user}</a>&nbsp;({$Monuments[j][k].id})
                        </td>
                        <td align="right">{$Monuments[j][k].value}</td>
                    </tr>
                {/section}
            </table>
{* !!! Display each monument -stop. *}
        </td>        
{* should we go to the next row? *}
        {if ! $smarty.section.j.last}
            {if !($smarty.section.j.rownum % 2)}
                </tr><tr>
            {/if}
        {else}
            </tr>
        {/if}
    {/section}
    </table>
{/if}
{if $Step == "shop"}
    {$Sinfo}<br /><br />
    <form method="post" action="thieves.php?step=shop&amp;buy">
    <input type="submit" value="{$Abuy}" /> <input type="text" name="amount" size="5" /> {$Tamount}
    </form>
{/if}
{if $Step == "missions"}
    {$Minfo}<br /><br />
    <ul>
    {section name=job loop=$Jobs}
        <li>{$Jobs[job]}<br />
        <a href="thieves.php?step=confirm&amp;number={$smarty.section.job.index}">{$Ayes}</a><br /><br /></li>
    {/section}
    </ul>
    {$Jobinfo2}
{/if}
{if $Step != ""}
<br /><br /><a href="thieves.php">{$Aback}</a>
{/if}
