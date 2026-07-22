import glob
files = glob.glob('*.html')
for f in files:
    with open(f, 'r', encoding='utf-8') as file:
        lines = file.readlines()
    for line in lines:
        if '<section class="page-hero"' in line or '<section class="grid-background"' in line:
            print(f'{f}: {line.strip()}')
