all: quickfw.pdf

quickfw.pdf: general quickstart directory mvc blocks features quickfw.txt
	a2x -v quickfw.txt --dblatex-opts='--backend=xetex -o quickfw.pdf'

general: general.txt
quickstart: quickstart.txt
directory: directory.txt
mvc: mvc.txt
blocks: blocks.txt

features: features.txt

clean:
	rm -rf quickfw.pdf


.PHONY: all clean