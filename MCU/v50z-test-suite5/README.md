# RatatoskrIoT: aplikace pro mikrokontroléry ESP8266 a ESP32

## **v43z-test-suite<#>**

Testovací sady pro kontrolu funkce knihoven. Musí fungovat na ESP32 i ESP8266.

---


# Knihovny a kód třetích stran

## Nutné knihovny v Arduino IDE
Pro všechny aplikace je nutné mít v library manageru nainstalováno:
- Tasker 2.0.0

## Knihovny a kód třetích stran 

Aplikace obsahují následující kód třetích stran ve formě zdrojových kódů distribuovaných přímo s aplikací (= nepoužívají se z library manageru):

### Tiny AES
- src\aes-sha\aes*
- zdroj: https://github.com/kokke/tiny-AES-c
- licence: public domain
- použito bez úprav

### CRC32
- src\aes-sha\CRC32*
- zdroj: https://github.com/bakercp/CRC32
- licence: MIT
- použito bez úprav

### SHA-256
- src\aes-sha\sha256*
- zdroj: https://github.com/CSSHL/ESP8266-Arduino-cryptolibs
- licence: public domain (dle https://github.com/B-Con/crypto-algorithms/blob/master/sha256.c)
- použito bez úprav

### dtostrg
- src\math\
- zdroj: https://github.com/tmrttmrt/dtostrg
- licence: MIT
- použito bez úprav

### tzapu/WiFiManager
- src\wifiman\
- zdroj: https://github.com/tzapu/WiFiManager
- licence: MIT
- provedeny úpravy (např. možnost načtení SSID a hesla)

### kmackay/micro-ecc
- src\micro_ecc\
- zdroj: https://github.com/kmackay/micro-ecc
- licence: BSD-2-Clause License
- přejmenovány .inc -> .h, jinak žádné úpravy


