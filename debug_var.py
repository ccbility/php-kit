import sublime
import sublime_plugin
import os.path
import re


class DebugVarCommand(sublime_plugin.TextCommand):
	#但一行有不止一个光标时，会出现bug
	def run(self, edit):
		file_name = self.view.file_name()
		ext = os.path.splitext(file_name)[1]
		# 获取文件的后缀，结果 .py .php .html .htm
		if ext == '.php':
			sep = ';'
			deal_fun = 'var_dump'
			end = 'die;'
		elif ext == '.html' or ext == '.htm' or ext == '.js':
			sep = ';'
			deal_fun = 'console.log'
			end = 'return false;'
		else:
			exit()

		i = 1
		total_len = len(self.view.sel())
		move_bool = True
		for region in self.view.sel():#所有光标所在的点
			# sublime.message_dialog(str(var_name))
			# var_name = self.view.substr(self.view.word(region.begin()))
			# 上面是第一版的粗糙获取变量
			line_cont = self.view.substr(self.view.line(region.begin()))
			# 如果当前行为空，那么直接输出结束符
			# 如果含 M D 这样的Thinkphp函数名，那么输出 getLastSql 或 _sql
			if line_cont == '':
				debug_str = ''
				move_bool = False
			elif '=' in line_cont:
				var_name = line_cont[0: line_cont.find('=')]
				#为了对齐，计算出tab的数量
				tab_num = var_name.count('\t')
				if tab_num > 0:
					tab_str = tab_num * '\t'
				else:
					tab_str = ''

				var_name = var_name.strip()
				# 针对js中的var
				if var_name.rfind(' ') > -1 : 
				#没找到空白时，返回一个-1，用-1去取数组的值会造成错误的结果
					var_name = var_name[var_name.rfind(' ') : ].strip()
				debug_str = "\n" + tab_str + deal_fun + '(' + var_name + ')' + sep;

			elif 'D(' in line_cont or 'M(' in line_cont:
				#没有考虑到行间和行尾tab的情况
				tab_num = line_cont.count('\t')
				if tab_num > 0:
					tab_str = tab_num * '\t'
				else:
					tab_str = ''

				match_cont = re.match(r'.*?([DM]+.*?\))', line_cont)
				debug_str = '\n' + tab_str + match_cont.group(1) + '->_sql();'

			if i == total_len:
				debug_str += end

			self.view.insert(edit, self.view.line(region).end(), debug_str)
			i += 1
		if move_bool:
			self.view.run_command("move", {"by": "lines", "forward": True})	