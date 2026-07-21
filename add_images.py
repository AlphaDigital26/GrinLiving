import os
import re

files_info = {
    'about-us.html': 'hero_about_us.jpg',
    'our-teams.html': 'hero_our_teams.jpg',
    'products.html': 'hero_products.jpg',
    'blog.html': 'hero_blog.jpg',
    'faqs.html': 'hero_faqs.jpg',
    'contact-us.html': 'hero_contact_us.jpg'
}

for filename, img in files_info.items():
    filepath = os.path.join(r'e:\GrinLiving', filename)
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    # We want to change the <section class="grid-background"> right after the Navbar
    # To <section class="page-hero inner-hero" style="background-image: url('Images/img'); margin-bottom: 0; padding-bottom: 0;">
    
    # 1. Find the section
    # Usually it is <section class="grid-background">
    # We will replace that specific one
    content = content.replace('<section class="grid-background">', f'<section class="page-hero inner-hero" style="background-image: url(\'Images/{img}\'); margin-bottom: 0; padding-bottom: 0;">', 1)
    
    # 2. Update text colors inside that section to white
    # We can isolate the section using regex
    pattern = r'(<section class="page-hero inner-hero".*?</section>)'
    
    def color_replacer(match):
        sec = match.group(1)
        sec = sec.replace('text-charcoal', 'text-pure-white opacity-80')
        # Also h1 display-lg needs text-pure-white
        sec = sec.replace('class="display-lg mt-16"', 'class="display-lg text-pure-white mt-16"')
        sec = sec.replace('class="display-lg"', 'class="display-lg text-pure-white"')
        sec = sec.replace('text-heritage-gold', 'text-heritage-gold') # this was for label, actually let's make labels gold
        sec = sec.replace('highlight mt-8', 'text-heritage-gold mt-8') # for faqs and contact us
        # for 'label-md text-charcoal' it became 'label-md text-pure-white opacity-80', let's fix it to text-heritage-gold
        sec = sec.replace('label-md text-pure-white opacity-80', 'label-md text-heritage-gold')
        return sec
        
    new_content = re.sub(pattern, color_replacer, content, flags=re.DOTALL)
    
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(new_content)
    
    print(f"Updated {filename}")
