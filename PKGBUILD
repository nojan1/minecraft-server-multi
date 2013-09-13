# Maintainer: Niklas Hedlund <nojan1989@gmail.com>

pkgname=minecraft-server-multi
pkgver=1.6.2
pkgrel=1
epoch=1
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
	status.php)
noextract=("minecraft_server.$pkgver.jar")  

md5sums=('39df9f29e6904ea7b351ffb4fe949881'
         '5e2f43167da374d2d725b19b6908fc9b'
         '64e793deed5856a970e92d0942168cfd'
         '161cfb8db6a1ac7c33be0184afc91865'
         '66cb4167175bf9b8cfd2d3a2b6ff4115'
         'b42821ecf13c4976d443e38cbb6f4a52'
         '6bc437f4a96bf225187e24b7d72317ed')

package() {
  install -Dm744 "$srcdir/minecraftd" "$pkgdir/usr/bin/minecraftd"
  #install -Dm744 "$srcdir/minecraftd-diag" "$pkgdir/usr/bin/minecraftd-diag"
  install -Dm744 "$srcdir/minecraftctl" "$pkgdir/usr/bin/minecraftctl"
  install -Dm644 "$srcdir/status.php" "$pkgdir/usr/share/minecraft-server/example/status.php"
  
  install -Dm644 "$srcdir/minecraft_server.$pkgver".jar "$pkgdir/srv/minecraft/common/minecraft_server.jar"
  install -Dm644 "$srcdir/minecraftd@.service" "$pkgdir/usr/lib/systemd/system/minecraftd@.service"
  install -Dm644 "$srcdir/conf.minecraft" "$pkgdir/etc/conf.d/minecraft"

  install -d "$pkgdir/srv/minecraft/backup"
}
