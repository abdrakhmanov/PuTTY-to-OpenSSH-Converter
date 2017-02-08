# PuTTY-to-OpenSSH

Convert your exported list of PuTTY accounts to a OpenSSH configuration file.

## Rationale: For those who SSH.

If you're moving from Windows to Linux you'll probably want to keep all your SSH accounts you've been using in [PuTTY](http://www.chiark.greenend.org.uk/~sgtatham/putty/).

On a Linux [OpenSSH](https://www.openssh.com/) will be the simplest tool help you keep track of all your SSH accounts.

To speed up the migration convert your list of accounts from PuTTY registery key to a OpenSSH configuration file format.

## Windows: Export Registry Keys

The below command should export all your sessions to the a file `putty.reg` on your desktop.

```
regedit /e "%userprofile%\desktop\putty.reg" HKEY_CURRENT_USER\Software\SimonTatham\PuTTY\Sessions
```

## Linux: Convert and Import to OpenSSH

1. Copy your exported PuTTY sessions to your Linux (with installed php).
2. Place the `putty.reg` file in the same directory as the `convert.php` script.
3. Run the `convert.php` in Terminal to generate a OpenSSH configuration file.

```
php convert.php putty.reg ssh_config
```

4. Copy the `ssh_config` file to `~/.ssh/config`.
```
cp ./ssh_config ~/.ssh/config
```

## Processed fields

 - HostName
 - PortNumber
 - UserName

## Links

https://www.openssh.com/

https://www.opennet.ru/cgi-bin/opennet/man.cgi?topic=ssh&category=1

http://www.chiark.greenend.org.uk/~sgtatham/putty/
