#!/usr/bin/env bash
# Uji akses manual via HTTP (curl) untuk 3 akun + tamu.
# Pemakaian:  php artisan serve   (terminal lain)   lalu   bash docs/uji-akses.sh
# Opsional :  BASE=http://127.0.0.1:8000 bash docs/uji-akses.sh
BASE="${BASE:-http://127.0.0.1:8000}"
PASS="password"
TMP="$(mktemp -d)"; trap 'rm -rf "$TMP"' EXIT

login() { # $1=email  $2=cookiejar
  local jar="$2"
  local token
  token=$(curl -s -c "$jar" "$BASE/login" | sed -n 's/.*name="_token" value="\([^"]*\)".*/\1/p' | head -1)
  curl -s -o /dev/null -b "$jar" -c "$jar" --data-urlencode "_token=$token" \
       --data-urlencode "email=$1" --data-urlencode "password=$PASS" "$BASE/login"
}

cek() { # $1=label  $2=cookiejar|-  $3=url  $4=harapan
  local jar="$2" url="$3" harapan="$4" out code loc hasil status
  if [ "$jar" = "-" ]; then out=$(curl -s -o /dev/null -w "%{http_code} %{redirect_url}" "$BASE$url")
  else out=$(curl -s -o /dev/null -b "$jar" -w "%{http_code} %{redirect_url}" "$BASE$url"); fi
  code="${out%% *}"; loc="${out#* }"
  case "$code" in
    200) hasil="Diizinkan (200)";;
    403) hasil="Ditolak (403)";;
    302) hasil="Dialihkan ke ${loc#$BASE}";;
    *)   hasil="HTTP $code";;
  esac
  [ "$hasil" = "$harapan" ] && status="LULUS" || status="GAGAL"
  printf "| %-10s | %-18s | %-26s | %-26s | %s |\n" "$1" "$url" "$harapan" "$hasil" "$status"
}

login admin@akademik.test     "$TMP/admin"
login dosen@akademik.test     "$TMP/dosen"
login mahasiswa@akademik.test "$TMP/mhs"

echo "| Pengguna   | URL                | Hasil yang diharapkan      | Hasil sebenarnya           | Status |"
echo "|------------|--------------------|----------------------------|----------------------------|--------|"
# --- 7 skenario dari materi ---
cek Admin     "$TMP/admin" /admin           "Diizinkan (200)"
cek Admin     "$TMP/admin" /kelola-materi   "Diizinkan (200)"
cek Dosen     "$TMP/dosen" /dosen           "Diizinkan (200)"
cek Dosen     "$TMP/dosen" /admin           "Ditolak (403)"
cek Mahasiswa "$TMP/mhs"   /mahasiswa       "Diizinkan (200)"
cek Mahasiswa "$TMP/mhs"   /kelola-materi   "Ditolak (403)"
cek Tamu      -            /admin           "Dialihkan ke /login"
# --- Manajemen pengguna ---
cek Admin     "$TMP/admin" /admin/pengguna  "Diizinkan (200)"
cek Dosen     "$TMP/dosen" /admin/pengguna  "Ditolak (403)"
cek Mahasiswa "$TMP/mhs"   /admin/pengguna  "Ditolak (403)"
# --- Materi ---
cek Admin     "$TMP/admin" /materi          "Diizinkan (200)"
cek Dosen     "$TMP/dosen" /materi          "Diizinkan (200)"
cek Mahasiswa "$TMP/mhs"   /materi          "Diizinkan (200)"
cek Dosen     "$TMP/dosen" /kelola-materi   "Diizinkan (200)"
# --- Nilai ---
cek Admin     "$TMP/admin" /kelola-nilai    "Diizinkan (200)"
cek Dosen     "$TMP/dosen" /kelola-nilai    "Diizinkan (200)"
cek Mahasiswa "$TMP/mhs"   /kelola-nilai    "Ditolak (403)"
cek Mahasiswa "$TMP/mhs"   /nilai-saya      "Diizinkan (200)"
cek Admin     "$TMP/admin" /nilai-saya      "Diizinkan (200)"
cek Dosen     "$TMP/dosen" /nilai-saya      "Ditolak (403)"
cek Tamu      -            /kelola-nilai    "Dialihkan ke /login"
cek Tamu      -            /nilai-saya      "Dialihkan ke /login"
# --- Halaman role lain (Least Privilege) ---
cek Admin     "$TMP/admin" /dosen           "Ditolak (403)"
cek Mahasiswa "$TMP/mhs"   /admin           "Ditolak (403)"
cek Mahasiswa "$TMP/mhs"   /dosen           "Ditolak (403)"
