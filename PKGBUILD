# Maintainer: Niklas Hedlund <nojan1989@gmail.com>

pkgname=minecraft-server-multi
pkgver=1.8.7
pkgrel=1
pkgdesc="Minecraft server unit files, script, and jar. Supports multiple server instances"
arch=(any)
url="http://minecraft.net/"
license=('custom')
depends=('java-runtime-headless' 'systemd' 'screen' 'expect')
conflicts=('minecraft-server-systemd' 'minecraft-canary' 'minecraft-server')
options=(emptydirs)
install=minecraft-server.install
backup=('etc/conf.d/minecraft')
source=(https://s3.amazonaws.com/Minecraft.Download/versions/$pkgver/minecraft_server.$pkgver.jar
        minecraftd
        minecraftd-diag
        minecraftd@.service
	minecraftctl
	conf.minecraft
	status.php
	minecraft-worldrotate@.service
	minecraft-worldrotate.sh)
noextract=("minecraft_server.$pkgver.jar")  

md5sums=('eca401ff4f8466645c222dce9471df66'
         'ce39b2612c536acc1c61d31cbcb9bbab'
         '9a17d3bdf37698c14d0348d011e3a7a1'
         '161cfb8db6a1ac7c33be0184afc91865'
         'a5b8429c2829b6c2255371dec6601740'
         'b42821ecf13c4976d443e38cbb6f4a52'
         '64aa876a3acc4ccec532e741cf1d7967'
         '8676d2f38c62e1c17c0c1e61dd16d2e0'
         '14929cc78fc684dfe5d8e4275b5eaa90')

package() {
  install -Dm744 "$srcdir/minecraftd" "$pkgdir/usr/bin/minecraftd"
  #install -Dm744 "$srcdir/minecraftd-diag" "$pkgdir/usr/bin/minecraftd-diag"
  install -Dm744 "$srcdir/minecraftctl" "$pkgdir/usr/bin/minecraftctl"
  install -Dm744 "$srcdir/minecraft-worldrotate.sh" "$pkgdir/usr/bin/minecraft-worldrotate.sh"
  install -Dm644 "$srcdir/status.php" "$pkgdir/usr/share/minecraft-server/example/status.php"
  
  install -Dm644 "$srcdir/minecraft_server.$pkgver".jar "$pkgdir/srv/minecraft/common/minecraft_server.jar"
  install -Dm644 "$srcdir/minecraftd@.service" "$pkgdir/usr/lib/systemd/system/minecraftd@.service"
  install -Dm644 "$srcdir/minecraft-worldrotate@.service" "$pkgdir/usr/lib/systemd/system/minecraft-worldrotate@.service"
  install -Dm644 "$srcdir/conf.minecraft" "$pkgdir/etc/conf.d/minecraft"

  install -d "$pkgdir/srv/minecraft/backup"
}
