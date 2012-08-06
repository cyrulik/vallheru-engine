{if $Action == ""}
    {$Notesinfo}<br /><br />
    {foreach $Notes as $Note}
        <label for="mytoggle{$Note@index}" class="toggle">&#171; {$Note.title} &#187;</label> [<a href="notatnik.php?akcja=skasuj&amp;nid={$Note.id}">{$Adelete}</a> | <a href="notatnik.php?akcja=edit&amp;nid={$Note.id}">{$Aedit}</a>]
        <input id="mytoggle{$Note@index}" type="checkbox" class="toggle" {$Checked} />
	<div>
            {$Ntime}: {$Note.czas}<br />{$Note.tekst}<br />
	</div><br /><br />
    {/foreach}
    {if $Tpages > 1}
    	<br />{$Fpage}
    	{for $page = 1 to $Tpages}
	    {if $page == $Tpage}
	        {$page}
	    {else}
                <a href="notatnik.php?page={$page}">{$page}</a>
	    {/if}
    	{/for}
    {/if}
    <br /><br /><a href="notatnik.php?akcja=dodaj">({$Aadd})</a>
{/if}

{if $Action == "edit" || $Action == "dodaj"}
    <script src="js/editor.js"></script>
    <form method="post" action="notatnik.php?akcja={$Nlink}" name="notes">
    <table>
    <tr><td>{$Ttitle}</td><td><input type="text" name="title" value="{$Ntitle}"></td></tr>
    <tr><td colspan="2"><input id="bold" type="button" value="{$Abold}" onClick="formatText(this.id, 'notes', 'body')" />
	<input id="italic" type="button" value="{$Aitalic}" onClick="formatText(this.id, 'notes', 'body')" />
	<input id="underline" type="button" value="{$Aunderline}" onClick="formatText(this.id, 'notes', 'body')" />
	<input id="emote" type="button" value="{$Aemote}" onClick="formatText(this.id, 'notes', 'body')" />
	<input id="center" type="button" value="{$Acenter}" onClick="formatText(this.id, 'notes', 'body')" />
	<input id="quote" type="button" value="{$Aquote}" onClick="formatText(this.id, 'notes', 'body')" />
	<input id="color" type="button" value="{$Acolor}" onClick="formatText(this.id, 'notes', 'body')" /> {html_options name=colors options=$Ocolors}</td></tr>
    <tr><td valign="top">{$Note}:</td><td><textarea name="body" rows="20" cols="40">{$Ntext}</textarea></td></tr>
    <tr><td colspan="2" align="center"><input type="submit" value="{$Asave}" /></td></tr>
    </table>
    </form><br /><br />
    (<a href="notatnik.php">{$Aback}</a>)
{/if}
