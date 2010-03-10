all: quickfw.pdf

quickfw.pdf: general quickstart directory mvc blocks  cache  auth  features quickfw.txt
	a2x -v quickfw.txt --dblatex-opts='--backend=xetex -o quickfw.pdf'

general: general.txt
quickstart: quickstart.txt
directory: directory.txt
mvc: mvc.txt
blocks: blocks.txt

cache: cache.txt

auth: auth.txt

features: features.txt


clean:
	rm -rf quickfw.pdf

.PHONY: all clean
