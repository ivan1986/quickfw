all: quickfw.pdf quickfw.html

FILES = quickfw general quickstart directory mvc blocks templates cache auth autoload features
TEXTS = $(addsuffix .txt, $(FILES) )

quickfw.pdf: $(TEXTS)
	a2x -v quickfw.txt --dblatex-opts='--backend=xetex -o quickfw.pdf' -a lang=ru

quickfw.html: $(TEXTS)
	a2x -f xhtml -v quickfw.txt -a lang=ru

site: ../../tmp/doc/quickfw.html $(TEXTS)

../../tmp/doc/quickfw.html: $(TEXTS)
	rm -rf ../../tmp/doc
	mkdir -p ../../tmp/doc
	a2x -f xhtml -v quickfw.txt -D ../../tmp/doc -a lang=ru

clean:
	rm -rf quickfw.pdf
	rm -rf quickfw.html docbook-xsl.css
	rm -rf ../../tmp/doc

.PHONY: all clean
