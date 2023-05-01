<template>
  <div class="app-container">
    <el-row>
      <el-col :span="12" class="card-box">
        <el-card>
          <div slot="header">
            <span>CPU</span>
          </div>
          <div class="el-table el-table--enable-row-hover el-table--medium">
            <table cellspacing="0" style="width: 100%;">
              <thead>
                <tr>
                  <th class="is-leaf">
                    <div class="cell">Attributes</div>
                  </th>
                  <th class="is-leaf">
                    <div class="cell">value</div>
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <div class="cell">Model</div>
                  </td>
                  <td>
                    <div class="cell" v-if="server.cpu_info">{{ server.cpu_info.model }}</div>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div class="cell">Core number</div>
                  </td>
                  <td>
                    <div class="cell" v-if="server.cpu_info">{{ server.cpu_info.num_text }}</div>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div class="cell">CPU frequency</div>
                  </td>
                  <td>
                    <div class="cell" v-if="server.cpu_info">{{ server.cpu_info.mhz }}%</div>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div class="cell">CPU cache</div>
                  </td>
                  <td>
                    <div class="cell" v-if="server.cpu_info">{{ server.cpu_info.cache }}%</div>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div class="cell">Bgomips</div>
                  </td>
                  <td>
                    <div class="cell" v-if="server.cpu_info">{{ server.cpu_info.bogomips }}%</div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </el-card>
      </el-col>

      <el-col :span="12" class="card-box">
        <el-card>
          <div slot="header">
            <span>Memory</span>
          </div>
          <div class="el-table el-table--enable-row-hover el-table--medium">
            <table cellspacing="0" style="width: 100%;">
              <thead>
                <tr>
                  <th class="is-leaf">
                    <div class="cell">Attributes</div>
                  </th>
                  <th class="is-leaf">
                    <div class="cell">Memory</div>
                  </th>
                  <th class="is-leaf">
                    <div class="cell">Reality</div>
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <div class="cell">Total memory</div>
                  </td>
                  <td>
                    <div class="cell" v-if="server.memory_info">{{ server.memory_info.mem_total }}MB</div>
                  </td>
                  <td>
                    <div class="cell" v-if="server.memory_info">{{ server.memory_info.mem_total }}MB</div>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div class="cell">Memory</div>
                  </td>
                  <td>
                    <div class="cell" v-if="server.memory_info">{{ server.memory_info.mem_used}}MB</div>
                  </td>
                  <td>
                    <div
                      class="cell"
                      v-if="server.memory_info"
                    >{{ server.memory_info.mem_real_used }}MB</div>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div class="cell">Free memory</div>
                  </td>
                  <td>
                    <div class="cell" v-if="server.memory_info">{{ server.memory_info.mem_free }}MB</div>
                  </td>
                  <td>
                    <div
                      class="cell"
                      v-if="server.memory_info"
                    >{{ server.memory_info.mem_real_free }}MB</div>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div class="cell">Use rate</div>
                  </td>
                  <td>
                    <div
                      class="cell"
                      v-if="server.memory_info"
                      :class="{'text-danger': server.memory_info.usage > 80}"
                    >{{ server.memory_info.mem_usage}}%</div>
                  </td>
                  <td>
                    <div
                      class="cell"
                      v-if="server.memory_info"
                    >{{ server.memory_info.mem_real_percent }}MB</div>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div class="cell">Cache memory</div>
                  </td>
                  <td>
                    <div
                      class="cell"
                      v-if="server.memory_info"
                    >{{ server.memory_info.mem_cached }}MB</div>
                  </td>
                  <td>
                    <div class="cell" v-if="server.memory_info">--</div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </el-card>
      </el-col>

      <el-col :span="24" class="card-box">
        <el-card>
          <div slot="header">
            <span>server information</span>
          </div>
          <div class="el-table el-table--enable-row-hover el-table--medium">
            <table cellspacing="0" style="width: 100%;">
              <tbody>
                <tr>
                  <td>
                    <div class="cell">name of server</div>
                  </td>
                  <td>
                    <div class="cell" v-if="server.serve_info">{{ server.serve_info.serve_name }}</div>
                  </td>
                  <td>
                    <div class="cell">operating system</div>
                  </td>
                  <td>
                    <div class="cell" v-if="server.serve_info">{{ server.serve_info.os }}</div>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div class="cell">Server running time</div>
                  </td>
                  <td>
                    <div class="cell" v-if="server.serve_info">{{ server.uptime }}</div>
                  </td>
                  <td>
                    <div class="cell">Project architecture</div>
                  </td>
                  <td>
                    <div class="cell" v-if="server.serve_info">{{ server.serve_info.architecture }}</div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </el-card>
      </el-col>

      <el-col :span="24" class="card-box">
        <el-card>
          <div slot="header">
            <span>Php information</span>
          </div>
          <div class="el-table el-table--enable-row-hover el-table--medium">
            <table cellspacing="0" style="width: 100%;">
              <tbody>
                <tr>
                  <td>
                    <div class="cell">PHP version</div>
                  </td>
                  <td>
                    <div class="cell" v-if="server.serve_info">{{ server.serve_info.php_version }}</div>
                  </td>
                  <td>
                    <div class="cell">PHP operation method</div>
                  </td>
                  <td>
                    <div class="cell" v-if="server.serve_info">{{ server.serve_info.php_run_type }}</div>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div class="cell">Zend version</div>
                  </td>
                  <td>
                    <div class="cell" v-if="server.serve_info">{{ server.serve_info.zend_version }}</div>
                  </td>
                  <td>
                    <div class="cell">Time of running</div>
                  </td>
                  <td>
                    <div class="cell" v-if="server.serve_info">{{ server.uptime}}</div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </el-card>
      </el-col>

      <!-- <el-col :span="24" class="card-box">
        <el-card>
          <div slot="header">
            <span>Disk state</span>
          </div>
          <div class="el-table el-table--enable-row-hover el-table--medium">
            <table cellspacing="0" style="width: 100%;">
              <thead>
                <tr>
                  <th class="is-leaf">
                    <div class="cell">Disk pathway</div>
                  </th>
                  <th class="is-leaf">
                    <div class="cell">File system</div>
                  </th>
                  <th class="is-leaf">
                    <div class="cell">Disk type</div>
                  </th>
                  <th class="is-leaf">
                    <div class="cell">Total size</div>
                  </th>
                  <th class="is-leaf">
                    <div class="cell">Available size</div>
                  </th>
                  <th class="is-leaf">
                    <div class="cell">Use size</div>
                  </th>
                  <th class="is-leaf">
                    <div class="cell">Percent</div>
                  </th>
                </tr>
              </thead>
              <tbody v-if="server.serve_infoFiles">
                <tr v-for="sysFile in server.serve_infoFiles">
                  <td>
                    <div class="cell">{{ sysFile.dirName }}</div>
                  </td>
                  <td>
                    <div class="cell">{{ sysFile.sysTypeName }}</div>
                  </td>
                  <td>
                    <div class="cell">{{ sysFile.typeName }}</div>
                  </td>
                  <td>
                    <div class="cell">{{ sysFile.total }}</div>
                  </td>
                  <td>
                    <div class="cell">{{ sysFile.free }}</div>
                  </td>
                  <td>
                    <div class="cell">{{ sysFile.used }}</div>
                  </td>
                  <td>
                    <div
                      class="cell"
                      :class="{'text-danger': sysFile.usage > 80}"
                    >{{ sysFile.usage }}%</div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </el-card>
      </el-col>-->
    </el-row>
  </div>
</template>

<script>
import { getServer } from '@/api/setting/monitoring_module/serve'

export default {
  name: 'Server',
  data() {
    return {
      //load layer information
      loading: [],
      //server information
      server: [],
    }
  },
  created() {
    this.getList()
    this.openLoading()
  },
  methods: {
    /**Query server information*/
    getList() {
      getServer().then((response) => {
        if (response.code == 200) {
          this.server = response.data
          this.loading.close()
        }
      })
    },
    //open loading layer
    openLoading() {
      this.loading = this.$loading({
        lock: true,
        text: 'Desperately read',
        spinner: 'el-icon-loading',
        background: 'rgba(0, 0, 0, 0.7)',
      })
    },
  },
}
</script>

<style lang="scss">
.card-box {
  padding-right: 15px;
  padding-left: 15px;
  margin-bottom: 10px;
}
</style>