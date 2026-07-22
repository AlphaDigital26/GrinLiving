import json

log_path = r'C:\Users\HP\.gemini\antigravity\brain\b624743e-50c2-47d7-a0a9-c0fa36d0f604\.system_generated\logs\transcript_full.jsonl'
with open(log_path, 'r', encoding='utf-8') as f:
    for line in f:
        data = json.loads(line)
        if data.get('type') == 'PLANNER_RESPONSE' and 'tool_calls' in data:
            for tc in data['tool_calls']:
                if tc['name'] == 'write_to_file':
                    args_str = str(tc.get('args', ''))
                    if 'our-process.html' in args_str and 'recover.py' not in args_str and '<!DOCTYPE html>' in args_str:
                        content = tc['args'].get('CodeContent', '')
                        if '<!-- Process Steps -->' in content or 'Industrial Luxury and Precision Manufacturing' in content:
                            with open('recovered_process.html', 'w', encoding='utf-8') as out:
                                out.write(content)
                            print("Successfully recovered to recovered_process.html")
                            break
