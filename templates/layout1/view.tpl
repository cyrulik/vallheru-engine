<center><b><u>{$User}</u></b> ({$Id})</center><br />
{$Avatar}
{$IP}<br />
{$GG}
<b>{$Tfreezed}</b>
{$Tlang}: {$Lang}<br />
{$Tseclang}
{$Trank}: {$Rank}<br />
{$Tlocation}: {$Location}<br />
{$Immu}
{$Tlastseen}: {$Page}<br />
{$Seen}: {$Lastseen}<br />
{$Tage}: {$Age}<br />
{$Trace}: {$Race}<br />
{$Tclass2}: {$Clas}<br />
{$Gender}
{$Deity}
{$Tlevel}: {$Level}<br />
{$Tstatus}: {$Status}
{$Clan}
{$Tmaxhp}: {$Maxhp}<br /><br />
{$Tfights}: {$Wins}/{$Losses} {$Fratio}<br />
{$Tlastkill}: {$Lastkilled}<br />
{$Tlastkilled}: {$Lastkilledby}<br />
{$Trefs}: {$Refs}<br />
{$Tprofile}:<br />{$Profile}<br /><br />
{if $Attack != "" || $Mail != "" || $Crime != ""}
    {$Toptions}:<br />
    <ul>
    {$Attack}
    {$Mail}
    {$Crime}
    {$Crime2}
    </ul>
{/if}
