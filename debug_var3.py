#coding: utf-8
import sublime
import sublime_plugin
import os.path
import re

def count_indent(line_cont):
	#不光是空格，还可能两者一起混合存在,所以不能分情况，必须一起统计出来
	match_cont = re.match(r'((\s*\t*)*)', line_cont)
	return match_cont.group(1)

class DebugVarCommand(sublime_plugin.TextCommand):
	#问题：
	#一行有不止一个光标时，会出现bug
	#一行太长的时候，光标没有停在应该的位置
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
			D_match = re.match(r'.*(D\([\s\'\"]*?' + var_name + '.*?\))\s*-', line_cont)
			M_match = re.match(r'.*(M\([\s\'\"]*?' + var_name + '.*?\))\s*-', line_cont)
			this_var = re.match(r'.*?(\$this->' + var_name + ')', line_cont)
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

			#处理php中，$this->这种情况
			elif this_var:
				indent_str = count_indent(line_cont)
				debug_str = "\n" + indent_str + deal_fun + '($this->'+ var_name + ')' + sep;

			elif tp_m:
				indent_str = count_indent(line_cont)
				debug_str = '\n' + indent_str + deal_fun + '(' + tp_m + '->_sql());'

			else:
				indent_str = count_indent(line_cont)
				debug_str = "\n" + indent_str + deal_fun + '(' + prex + var_name + ')' + sep;

			if i == total_len:
				debug_str += end

			self.view.insert(edit, self.view.line(region).end(), debug_str)
			i += 1
		if move_bool:
			self.view.run_command("move", {"by": "lines", "forward": True})	