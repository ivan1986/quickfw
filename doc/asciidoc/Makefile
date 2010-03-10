all: pdf html-all

pdf: quickfw
	a2x -v quickfw.txt --dblatex-opts='--backend=xetex -o quickfw.pdf'
html-all: quickfw
	a2x -f xhtml -v quickfw.txt

quickfw: general quickstart directory mvc blocks  cache  auth  features quickfw.txt

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
	rm -rf quickfw.html docbook-xsl.css

.PHONY: all clean
