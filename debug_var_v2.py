import sublime
import sublime_plugin
import os.path
import re


class DebugVarCommand(sublime_plugin.TextCommand):
	#一行有不止一个光标时，会出现bug
	def run(self, edit):
		file_name = self.view.file_name()
		ext = os.path.splitext(file_name)[1]
		# 获取文件的后缀，结果 .py .php .html .htm
		if ext == '.php':
			prex = '$'
			sep = ';'
			deal_fun = 'var_dump'
			end = 'die;'
		elif ext == '.html' or ext == '.htm' or ext == '.js':
			prex = ''
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
			var_name = self.view.substr(self.view.word(region.begin()))
			line_cont = self.view.substr(self.view.line(region.begin()))

			# 如果当前行为空，那么直接输出结束符
			# 如果含 M D 这样的Thinkphp函数名，那么输出 getLastSql 或 _sql
			# 
			# python可以边赋值边判断真假？
			D_match = re.match(r'.*(D\(.*?' + var_name + '.*?\))\s*-', line_cont)
			M_match = re.match(r'.*(M\(.*?' + var_name + '.*?\))\s*-', line_cont)
			if D_match:
				tp_m = D_match.group(1)
			elif M_match:
				tp_m = M_match.group(1)
			else:
				tp_m = False
			if line_cont == '':
				debug_str = ''	
				move_bool = False
				#当前行为空时，直接输出一个结束符

			elif tp_m:
				match_cont = re.match(r'(\t*)', line_cont)
				#为了对齐，计算出tab的数量，或者空格的个数
				tab_num = len(match_cont.group(1))
				if tab_num > 0:
					tab_str = tab_num * '\t'
				else:
					match_cont = re.match(r'(\s*)', line_cont)
					space_num = len(match_cont.group(1))
					if space_num > 0:
						tab_str = space_num * ' '

				debug_str = '\n' + tab_str + tp_m + '->_sql();'

			else:
				match_cont = re.match(r'(\t*)', line_cont)
				#为了对齐，计算出tab的数量，或者空格的个数
				tab_num = len(match_cont.group(1))
				if tab_num > 0:
					tab_str = tab_num * '\t'
				else:
					match_cont = re.match(r'(\s*)', line_cont)
					space_num = len(match_cont.group(1))
					if space_num > 0:
						tab_str = space_num * ' '

				debug_str = "\n" + tab_str + deal_fun + '(' + prex + var_name + ')' + sep;

			if i == total_len:
				debug_str += end

			self.view.insert(edit, self.view.line(region).end(), debug_str)
			i += 1
		if move_bool:
			self.view.run_command("move", {"by": "lines", "forward": True})	