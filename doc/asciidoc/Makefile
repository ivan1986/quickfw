all: pdf html

FILES = quickfw general quickstart directory mvc blocks templates cache auth autoload helpers features
TEXTS = $(addsuffix .txt, $(FILES) )

pdf: quickfw.pdf

html: quickfw.html

quickfw.pdf: $(TEXTS)
	a2x -v quickfw.txt --dblatex-opts='--backend=xetex -o quickfw.pdf' -a lang=ru

quickfw.html: $(TEXTS)
	a2x -f xhtml -v quickfw.txt -a lang=ru

site: ../../var/tmp/doc/quickfw.html $(TEXTS)

../../var/tmp/doc/quickfw.html: $(TEXTS)
	rm -rf ../../var/tmp/doc
	mkdir -p ../../var/tmp/doc
	a2x -f xhtml -v quickfw.txt -D ../../var/tmp/doc -a lang=ru

clean:
	rm -rf quickfw.pdf
	rm -rf quickfw.html docbook-xsl.css
	rm -rf ../../var/tmp/doc

install:
	sudo aptitude install asciidoc texlive-xetex source-highlight

.PHONY: all clean install
