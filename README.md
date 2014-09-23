# PuTTY-to-Shuttle

Convert your exported list of PuTTY accounts to a Shuttle configuration file.

## Rationale: For those who SSH.

If you're moving from Windows to Mac you'll probably want to keep all your SSH accounts you've been using in [PuTTY](http://www.chiark.greenend.org.uk/~sgtatham/putty/).

On a Mac [Shuttle](http://fitztrev.github.io/shuttle/) will be the simplest tool help you keep track of all your SSH accounts.

To speed up the migration convert your list of accounts from PuTTY registery key to a Shuttle format which is in JSON.

## Windows: Export Registery Keys

The below command should export all your sessions to the a file `putty.reg` on your desktop.

```
regedit /e "%userprofile%\desktop\putty.reg" HKEY_CURRENT_USER\Software\SimonTatham\PuTTY\Sessions
```

## Mac: Convert and Import to Shuttle

1. Copy your exported PuTTY sessions to your Mac running Shuttle.
2. Place the `putty.reg` file in the same directory as the `convert.php` script. 
3. Run the `convert.php` in Terminal to generate a `shuttle.json` file. 

```
php convert.php putty.reg shuttle.json
```

4. Copy the `shuttle.json` file to `~/.shuttle.json` and check Shuttle.
```
cp ./shuttle.json ~/.shuttle.json
```

## Links

https://github.com/fitztrev/shuttle
http://www.chiark.greenend.org.uk/~sgtatham/putty/
