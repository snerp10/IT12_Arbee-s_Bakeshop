import os

# File extensions to include
extensions = ('.php', '.blade.php', '.js', '.css')

# Output file
output_file = 'all_code.txt'


def should_include(file_path):
    # Normalize path for consistent matching
    path = file_path.lower().replace('\\', '/').replace('./', '')
    # Only include files with the right extension
    if not path.endswith(extensions):
        return False
    # Exclude non-essential files/folders
    non_essential = [
        'test', 'seed', 'example', 'demo', 'branding', 'vendor', 'welcome', 'landing',
        '.svg', '.md', '.txt', '.json', '.lock', 'readme', 'sample', 'docs', 'doc'
    ]
    if any(x in path for x in non_essential):
        return False
    # Only include if in app, routes, config, database, resources/views, resources/js, resources/css, public/css
    essentials = [
        'app/', 'routes/', 'config/', 'database/', 'resources/views/', 'resources/js/', 'resources/css/', 'public/css/'
    ]
    if any(path.startswith(e) for e in essentials):
        return True
    return False


with open(output_file, 'w', encoding='utf-8') as outfile:
    for root, dirs, files in os.walk('.'):
        for file in files:
            file_path = os.path.join(root, file)
            rel_path = os.path.relpath(file_path, '.')
            if should_include(rel_path):
                outfile.write(f'File: {file_path}\n')
                try:
                    with open(file_path, 'r', encoding='utf-8', errors='ignore') as infile:
                        outfile.write(infile.read())
                except Exception as e:
                    outfile.write(f'[Error reading file: {e}]\n')
                outfile.write('\n\n--- End of File ---\n\n')

print(f'All backend and UI code extracted to {output_file}')