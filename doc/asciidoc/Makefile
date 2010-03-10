all: quickfw.pdf quickfw.html

FILES = quickfw general quickstart directory mvc blocks cache auth features
TEXTS = $(addsuffix .txt, $(FILES) )

quickfw.pdf: $(TEXTS)
	a2x -v quickfw.txt --dblatex-opts='--backend=xetex -o quickfw.pdf'

quickfw.html: $(TEXTS)
	a2x -f xhtml -v quickfw.txt

site: ../../tmp/doc/quickfw.html $(TEXTS)

../../tmp/doc/quickfw.html: $(TEXTS)
	rm -rf ../../tmp/doc
	mkdir -p ../../tmp/doc
	a2x -f xhtml -v quickfw.txt -D ../../tmp/doc

clean:
	rm -rf quickfw.pdf
	rm -rf quickfw.html docbook-xsl.css
	rm -rf ../../tmp/doc

.PHONY: all clean
