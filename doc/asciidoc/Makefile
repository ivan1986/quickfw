all: quickfw.pdf quickfw.html

quickfw.pdf: quickfw.txt
	a2x -v quickfw.txt --dblatex-opts='--backend=xetex -o quickfw.pdf'

quickfw.html: quickfw.txt 
	a2x -f xhtml -v quickfw.txt

FILES = general quickstart directory mvc blocks cache auth features

quickfw.txt: $(addsuffix .txt, $(FILES))
	touch $@

site: ../../tmp/doc/quickfw.html

../../tmp/doc/quickfw.html: quickfw.txt $(addsuffix .txt, $(FILES))
	rm -rf ../../tmp/doc
	mkdir -p ../../tmp/doc
	a2x -f xhtml -v quickfw.txt -D ../../tmp/doc

clean:
	rm -rf quickfw.pdf
	rm -rf quickfw.html docbook-xsl.css
	rm -rf ../../tmp/doc

.PHONY: all clean
